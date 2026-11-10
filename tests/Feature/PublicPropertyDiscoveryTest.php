<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPropertyDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected PropertyCategory $landCategory;
    protected PropertyCategory $residentialCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->landCategory = PropertyCategory::factory()->create([
            'name' => 'Land',
            'slug' => 'land',
            'code_prefix' => 'LAND',
        ]);

        $this->residentialCategory = PropertyCategory::factory()->create([
            'name' => 'Residential',
            'slug' => 'residential',
            'code_prefix' => 'PROP',
        ]);
    }

    public function test_public_listings_only_display_published_properties(): void
    {
        // Published
        Property::factory()->create([
            'category_id' => $this->landCategory->id,
            'title' => 'Available Land in Epe',
            'status' => Property::STATUS_PUBLISHED,
        ]);

        // Non-public statuses
        Property::factory()->create([
            'category_id' => $this->landCategory->id,
            'title' => 'Secret Draft Parcel',
            'status' => Property::STATUS_DRAFT,
        ]);

        Property::factory()->create([
            'category_id' => $this->residentialCategory->id,
            'title' => 'Sold Out Lekki Terrace',
            'status' => Property::STATUS_SOLD,
        ]);

        Property::factory()->create([
            'category_id' => $this->residentialCategory->id,
            'title' => 'Reserved Ikoyi Penthouse',
            'status' => Property::STATUS_RESERVED,
        ]);

        $response = $this->get('/properties');

        $response->assertStatus(200);
        $response->assertSeeText('Available Land in Epe');
        $response->assertDontSeeText('Secret Draft Parcel');
        $response->assertDontSeeText('Sold Out Lekki Terrace');
        $response->assertDontSeeText('Reserved Ikoyi Penthouse');
    }

    public function test_category_filter_returns_matching_properties(): void
    {
        Property::factory()->create([
            'category_id' => $this->landCategory->id,
            'title' => 'Epe Farmland Acreage',
            'status' => Property::STATUS_PUBLISHED,
        ]);

        Property::factory()->create([
            'category_id' => $this->residentialCategory->id,
            'title' => 'Chevron 4-Bed Duplex',
            'status' => Property::STATUS_PUBLISHED,
        ]);

        $landResponse = $this->get('/properties?category=land');
        $landResponse->assertStatus(200);
        $landResponse->assertSeeText('Epe Farmland Acreage');
        $landResponse->assertDontSeeText('Chevron 4-Bed Duplex');

        $resResponse = $this->get('/properties?category=residential');
        $resResponse->assertStatus(200);
        $resResponse->assertSeeText('Chevron 4-Bed Duplex');
        $resResponse->assertDontSeeText('Epe Farmland Acreage');
    }

    public function test_location_filters_return_matching_properties(): void
    {
        Property::factory()->create([
            'title' => 'Lagos Coastal Plot',
            'state' => 'Lagos',
            'lga' => 'Ibeju-Lekki',
            'neighbourhood' => 'Eleko',
            'status' => Property::STATUS_PUBLISHED,
        ]);

        Property::factory()->create([
            'title' => 'Abuja Diplomatic Zone',
            'state' => 'Abuja (FCT)',
            'lga' => 'Municipal Area Council',
            'neighbourhood' => 'Maitama',
            'status' => Property::STATUS_PUBLISHED,
        ]);

        $response = $this->get('/properties?state=Lagos&lga=Ibeju-Lekki');
        $response->assertStatus(200);
        $response->assertSeeText('Lagos Coastal Plot');
        $response->assertDontSeeText('Abuja Diplomatic Zone');
    }

    public function test_price_filter_returns_within_range(): void
    {
        Property::factory()->create([
            'title' => 'Affordable Plot',
            'price' => 15000000,
            'status' => Property::STATUS_PUBLISHED,
        ]);

        Property::factory()->create([
            'title' => 'Ultra Luxury Mansion',
            'price' => 450000000,
            'status' => Property::STATUS_PUBLISHED,
        ]);

        $response = $this->get('/properties?max_price=50000000');
        $response->assertStatus(200);
        $response->assertSeeText('Affordable Plot');
        $response->assertDontSeeText('Ultra Luxury Mansion');
    }

    public function test_keyword_filter_matches_title_and_reference(): void
    {
        Property::factory()->create([
            'title' => 'Exclusive Golf Estate Villa',
            'reference' => 'PROP-8899',
            'status' => Property::STATUS_PUBLISHED,
        ]);

        Property::factory()->create([
            'title' => 'Commercial Shop',
            'reference' => 'COMM-1122',
            'status' => Property::STATUS_PUBLISHED,
        ]);

        $response = $this->get('/properties?keyword=PROP-8899');
        $response->assertStatus(200);
        $response->assertSeeText('Exclusive Golf Estate Villa');
        $response->assertDontSeeText('Commercial Shop');
    }

    public function test_public_detail_page_renders_for_published_property(): void
    {
        $property = Property::factory()->create([
            'category_id' => $this->landCategory->id,
            'reference' => 'LAND-0042',
            'title' => 'Prime Beachfront Plot',
            'plot_size' => '800 sqm',
            'title_type' => 'Governor\'s Consent',
            'status' => Property::STATUS_PUBLISHED,
        ]);

        $response = $this->get("/properties/{$property->slug}");

        $response->assertStatus(200);
        $response->assertSeeText('Prime Beachfront Plot');
        $response->assertSeeText('LAND-0042');
        $response->assertSeeText('800 sqm');
        $response->assertSee('https://wa.me/2348000000000', false);
    }

    public function test_draft_property_returns_404_for_guest(): void
    {
        $draftProperty = Property::factory()->create([
            'status' => Property::STATUS_DRAFT,
        ]);

        $response = $this->get("/properties/{$draftProperty->slug}");

        $response->assertStatus(404);
    }

    public function test_draft_property_can_be_previewed_by_logged_in_admin(): void
    {
        $admin = User::factory()->create();

        $draftProperty = Property::factory()->create([
            'status' => Property::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($admin)->get("/properties/{$draftProperty->slug}");

        $response->assertStatus(200);
        $response->assertSeeText($draftProperty->title);
    }
}
