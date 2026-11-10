<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyMediaAndDocumentSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_admin_can_download_private_and_public_documents(): void
    {
        $property = Property::factory()->create();

        $file = UploadedFile::fake()->create('private_title.pdf', 500, 'application/pdf');
        $filePath = $file->store('properties/docs', 'local');

        $document = PropertyDocument::create([
            'property_id' => $property->id,
            'title' => 'Governor Consent Extract',
            'document_type' => "Governor's Consent",
            'file_path' => $filePath,
            'file_name' => 'private_title.pdf',
            'file_size' => 500 * 1024,
            'mime_type' => 'application/pdf',
            'is_public' => false, // strictly private
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/properties/{$property->id}/documents/{$document->id}/download");

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }

    public function test_guest_cannot_download_private_document(): void
    {
        $property = Property::factory()->create([
            'status' => Property::STATUS_PUBLISHED,
        ]);

        $file = UploadedFile::fake()->create('private_deed.pdf', 500, 'application/pdf');
        $filePath = $file->store('properties/docs', 'local');

        $document = PropertyDocument::create([
            'property_id' => $property->id,
            'title' => 'Private Deed of Assignment',
            'document_type' => 'Deed of Assignment',
            'file_path' => $filePath,
            'file_name' => 'private_deed.pdf',
            'file_size' => 500 * 1024,
            'mime_type' => 'application/pdf',
            'is_public' => false, // private
        ]);

        // Attempting to download via public route
        $response = $this->get("/properties/{$property->slug}/documents/{$document->id}");

        $response->assertStatus(403);
    }

    public function test_guest_can_download_public_document_on_published_property(): void
    {
        $property = Property::factory()->create([
            'status' => Property::STATUS_PUBLISHED,
        ]);

        $file = UploadedFile::fake()->create('approved_layout.pdf', 500, 'application/pdf');
        $filePath = $file->store('properties/docs', 'local');

        $document = PropertyDocument::create([
            'property_id' => $property->id,
            'title' => 'Approved Estate Layout',
            'document_type' => 'Survey Plan',
            'file_path' => $filePath,
            'file_name' => 'approved_layout.pdf',
            'file_size' => 500 * 1024,
            'mime_type' => 'application/pdf',
            'is_public' => true, // marked public
        ]);

        $response = $this->get("/properties/{$property->slug}/documents/{$document->id}");

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }

    public function test_guest_cannot_download_public_document_if_property_is_draft(): void
    {
        $property = Property::factory()->create([
            'status' => Property::STATUS_DRAFT,
        ]);

        $file = UploadedFile::fake()->create('sample.pdf', 500, 'application/pdf');
        $filePath = $file->store('properties/docs', 'local');

        $document = PropertyDocument::create([
            'property_id' => $property->id,
            'title' => 'Brochure',
            'document_type' => 'Other',
            'file_path' => $filePath,
            'file_name' => 'sample.pdf',
            'file_size' => 500 * 1024,
            'mime_type' => 'application/pdf',
            'is_public' => true,
        ]);

        $response = $this->get("/properties/{$property->slug}/documents/{$document->id}");

        $response->assertStatus(404);
    }
}
