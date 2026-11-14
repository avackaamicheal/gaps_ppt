<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAndSitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_valid_xml_with_published_properties(): void
    {
        $category = PropertyCategory::factory()->create([
            'name' => 'Land',
            'slug' => 'land',
            'code_prefix' => 'LND',
        ]);

        $publishedProperty = Property::create([
            'category_id' => $category->id,
            'reference' => 'LND-PUB-001',
            'title' => 'Prime Beachfront Plot',
            'slug' => 'prime-beachfront-plot',
            'description' => 'Beautiful beachfront land with C of O.',
            'price' => 75000000,
            'currency' => 'NGN',
            'status' => Property::STATUS_PUBLISHED,
            'published_at' => now(),
            'state' => 'Lagos',
            'lga' => 'Ibeju-Lekki',
            'neighbourhood' => 'Eleko',
        ]);

        $draftProperty = Property::create([
            'category_id' => $category->id,
            'reference' => 'LND-DFT-002',
            'title' => 'Hidden Draft Parcel',
            'slug' => 'hidden-draft-parcel',
            'description' => 'Draft unapproved parcel.',
            'price' => 30000000,
            'currency' => 'NGN',
            'status' => Property::STATUS_DRAFT,
            'state' => 'Lagos',
            'lga' => 'Epe',
            'neighbourhood' => 'Alaro City',
        ]);

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $response->assertSee(route('home'));
        $response->assertSee(route('properties.index'));
        $response->assertSee(route('properties.show', $publishedProperty->slug));
        $response->assertDontSee(route('properties.show', $draftProperty->slug));
    }

    public function test_robots_txt_disallows_admin_and_points_to_sitemap(): void
    {
        $robotsContent = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Disallow: /admin/', $robotsContent);
        $this->assertStringContainsString('Sitemap: /sitemap.xml', $robotsContent);
    }

    public function test_property_show_page_renders_opengraph_meta_tags(): void
    {
        $category = PropertyCategory::factory()->create([
            'name' => 'Residential',
            'slug' => 'residential',
            'code_prefix' => 'RES',
        ]);

        $property = Property::create([
            'category_id' => $category->id,
            'reference' => 'BLD-OG-101',
            'title' => 'Luxury 5 Bedroom Penthouse',
            'slug' => 'luxury-5-bedroom-penthouse',
            'description' => 'Architectural masterpiece overlooking Ikoyi with Governor Consent.',
            'price' => 250000000,
            'currency' => 'NGN',
            'status' => Property::STATUS_PUBLISHED,
            'published_at' => now(),
            'state' => 'Lagos',
            'lga' => 'Eti-Osa',
            'neighbourhood' => 'Old Ikoyi',
        ]);

        $response = $this->get(route('properties.show', $property->slug));

        $response->assertOk();
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:description"', false);
        $response->assertSee('property="og:image"', false);
        $response->assertSee('name="twitter:card"', false);
    }
}
