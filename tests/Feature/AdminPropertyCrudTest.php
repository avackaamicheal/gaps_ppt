<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyDocument;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPropertyCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected PropertyCategory $landCategory;
    protected PropertyCategory $residentialCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

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

        Storage::fake('public');
        Storage::fake('local');
    }

    public function test_unauthenticated_user_cannot_access_admin_property_routes(): void
    {
        $property = Property::factory()->create();

        $this->get('/admin/properties')->assertRedirect('/admin/login');
        $this->get('/admin/properties/create')->assertRedirect('/admin/login');
        $this->post('/admin/properties', [])->assertRedirect('/admin/login');
        $this->get("/admin/properties/{$property->id}")->assertRedirect('/admin/login');
        $this->get("/admin/properties/{$property->id}/edit")->assertRedirect('/admin/login');
        $this->put("/admin/properties/{$property->id}", [])->assertRedirect('/admin/login');
        $this->delete("/admin/properties/{$property->id}")->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_property_index_with_filters(): void
    {
        Property::factory()->create([
            'category_id' => $this->landCategory->id,
            'title' => 'Epe Dry Land Parcel',
            'status' => Property::STATUS_PUBLISHED,
        ]);

        Property::factory()->create([
            'category_id' => $this->residentialCategory->id,
            'title' => 'Ikoyi Luxury Villa',
            'status' => Property::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/properties');
        $response->assertStatus(200);
        $response->assertSeeText('Epe Dry Land Parcel');
        $response->assertSeeText('Ikoyi Luxury Villa');

        // Filter by category
        $catFilter = $this->actingAs($this->admin)->get('/admin/properties?category=' . $this->landCategory->id);
        $catFilter->assertSeeText('Epe Dry Land Parcel');
        $catFilter->assertDontSeeText('Ikoyi Luxury Villa');

        // Filter by status
        $statusFilter = $this->actingAs($this->admin)->get('/admin/properties?status=draft');
        $statusFilter->assertSeeText('Ikoyi Luxury Villa');
        $statusFilter->assertDontSeeText('Epe Dry Land Parcel');
    }

    public function test_admin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/properties/create');

        $response->assertStatus(200);
        $response->assertSee('Add New Property Listing');
        $response->assertSeeText('Land Attributes & Title Documentation');
    }

    public function test_admin_can_create_land_property_with_images_and_documents(): void
    {
        $imageFile = UploadedFile::fake()->image('plot.jpg', 800, 600);
        $docFile = UploadedFile::fake()->create('survey.pdf', 1000, 'application/pdf');

        $payload = [
            'category_id' => $this->landCategory->id,
            'title' => 'Prime 2 Plots in Ibeju Lekki',
            'description' => 'Excellent investment land along the coastal road with complete excision.',
            'price' => 25000000,
            'status' => Property::STATUS_PUBLISHED,
            'property_type' => 'Commercial Land',
            'is_featured' => 1,
            'state' => 'Lagos',
            'lga' => 'Ibeju-Lekki',
            'neighbourhood' => 'Eleko',
            'street_address' => 'Off Eleko Junction',
            'plot_size' => '1200 sqm',
            'plot_dimensions' => '120ft x 120ft',
            'number_of_plots' => 2,
            'land_use' => 'Commercial',
            'title_type' => 'Certificate of Occupancy (C of O)',
            'topography' => '100% Dry Land',
            'has_survey' => 1,
            'images' => [$imageFile],
            'cover_index' => 0,
            'documents' => [
                [
                    'title' => 'Registered Survey Plan',
                    'type' => 'Survey Plan',
                    'file' => $docFile,
                    'is_public' => 0, // private by default
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/properties', $payload);

        $response->assertRedirect('/admin/properties');
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('properties', [
            'title' => 'Prime 2 Plots in Ibeju Lekki',
            'reference' => 'LAND-0001',
            'price' => 25000000,
            'status' => Property::STATUS_PUBLISHED,
            'state' => 'Lagos',
            'is_featured' => 1,
        ]);

        $property = Property::where('reference', 'LAND-0001')->first();
        $this->assertNotNull($property->published_at);
        $this->assertEquals(1, $property->images()->count());
        $this->assertEquals(1, $property->documents()->count());

        $image = $property->images()->first();
        $this->assertTrue($image->is_cover);
        Storage::disk('public')->assertExists($image->file_path);

        $document = $property->documents()->first();
        $this->assertFalse($document->is_public);
        Storage::disk('local')->assertExists($document->file_path);
    }

    public function test_admin_can_create_building_property(): void
    {
        $payload = [
            'category_id' => $this->residentialCategory->id,
            'title' => 'Brand New 5 Bedroom Detached Duplex',
            'description' => 'Contemporary architecture with private swimming pool and automated gate.',
            'price' => 160000000,
            'status' => Property::STATUS_DRAFT,
            'property_type' => 'Detached Duplex',
            'state' => 'Lagos',
            'lga' => 'Eti-Osa',
            'neighbourhood' => 'Lekki Phase 1',
            'bedrooms' => 5,
            'bathrooms' => 5,
            'toilets' => 6,
            'parking_spaces' => 4,
            'floors' => 2,
            'building_condition' => 'Newly Built',
            'features' => ['Private Swimming Pool', 'Fitted Kitchen with Heat Extractor', 'Automated Gate / Smart Home'],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/properties', $payload);

        $response->assertRedirect('/admin/properties');

        $this->assertDatabaseHas('properties', [
            'reference' => 'PROP-0001',
            'bedrooms' => 5,
            'status' => Property::STATUS_DRAFT,
        ]);

        $property = Property::where('reference', 'PROP-0001')->first();
        $this->assertNull($property->published_at);
        $this->assertContains('Private Swimming Pool', $property->features);
    }

    public function test_admin_can_update_property_attributes(): void
    {
        $property = Property::factory()->create([
            'category_id' => $this->residentialCategory->id,
            'reference' => 'PROP-0001',
            'price' => 100000000,
            'status' => Property::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/properties/{$property->id}", [
            'category_id' => $this->residentialCategory->id,
            'title' => 'Updated Property Title',
            'description' => $property->description,
            'price' => 120000000,
            'status' => Property::STATUS_PUBLISHED,
            'state' => 'Lagos',
            'lga' => 'Eti-Osa',
            'neighbourhood' => 'Ikoyi',
        ]);

        $response->assertRedirect('/admin/properties');

        $property->refresh();
        $this->assertEquals(120000000, $property->price);
        $this->assertEquals('Updated Property Title', $property->title);
        $this->assertEquals(Property::STATUS_PUBLISHED, $property->status);
        $this->assertNotNull($property->published_at);
    }

    public function test_admin_can_update_property_status(): void
    {
        $property = Property::factory()->create([
            'status' => Property::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($this->admin)->patch("/admin/properties/{$property->id}/status", [
            'status' => Property::STATUS_PUBLISHED,
        ]);

        $response->assertRedirect();
        $property->refresh();
        $this->assertEquals(Property::STATUS_PUBLISHED, $property->status);
        $this->assertNotNull($property->published_at);
    }

    public function test_admin_can_delete_property_and_files(): void
    {
        $property = Property::factory()->create();

        $imageFile = UploadedFile::fake()->image('img.jpg');
        $docFile = UploadedFile::fake()->create('doc.pdf');

        $imgPath = $imageFile->store('properties/test', 'public');
        $docPath = $docFile->store('properties/test', 'local');

        $image = PropertyImage::create([
            'property_id' => $property->id,
            'file_path' => $imgPath,
        ]);

        $doc = PropertyDocument::create([
            'property_id' => $property->id,
            'title' => 'Test Doc',
            'document_type' => 'Other',
            'file_path' => $docPath,
            'file_name' => 'doc.pdf',
            'file_size' => 100,
            'mime_type' => 'application/pdf',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/properties/{$property->id}");

        $response->assertRedirect('/admin/properties');

        $this->assertSoftDeleted('properties', ['id' => $property->id]);
        Storage::disk('public')->assertMissing($imgPath);
        Storage::disk('local')->assertMissing($docPath);
    }
}
