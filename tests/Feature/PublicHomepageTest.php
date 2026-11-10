<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        PropertyCategory::factory()->create([
            'name' => 'Land',
            'slug' => 'land',
            'code_prefix' => 'LAND',
        ]);

        PropertyCategory::factory()->create([
            'name' => 'Residential',
            'slug' => 'residential',
            'code_prefix' => 'PROP',
        ]);

        PropertyCategory::factory()->create([
            'name' => 'Commercial',
            'slug' => 'commercial',
            'code_prefix' => 'COMM',
        ]);
    }

    public function test_public_homepage_loads_successfully_without_authentication(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Victor Akande');
        $response->assertSeeText('Independent Real Estate Consultant');
    }

    public function test_public_homepage_contains_branding_and_cta_elements(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Secure Verified Land & Prime Properties Across Nigeria');
        $response->assertSeeText('+234 800 000 0000');
        $response->assertSee('https://wa.me/2348000000000', false);
    }

    public function test_public_homepage_contains_category_navigation_and_featured_listings(): void
    {
        Property::factory()->create([
            'title' => 'Exclusive Lekki Villa',
            'is_featured' => true,
            'status' => Property::STATUS_PUBLISHED,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Explore by Category');
        $response->assertSeeText('Featured Properties & Land');
        $response->assertSeeText('Exclusive Lekki Villa');
    }

    public function test_public_homepage_contains_about_section(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Personal Integrity, Thorough Due Diligence');
        $response->assertSeeText('Verified Documentation');
    }
}
