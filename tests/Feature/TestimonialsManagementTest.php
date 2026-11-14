<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_guest_cannot_access_testimonials_admin(): void
    {
        $response = $this->get(route('admin.testimonials.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_testimonials_index(): void
    {
        $category = PropertyCategory::factory()->create([
            'name' => 'Land',
            'slug' => 'land',
            'code_prefix' => 'LND',
        ]);

        $property = Property::create([
            'category_id' => $category->id,
            'reference' => 'LND-TEST-999',
            'title' => 'Test Commercial Acreage',
            'slug' => 'test-commercial-acreage',
            'description' => 'Test description',
            'price' => 50000000,
            'currency' => 'NGN',
            'status' => Property::STATUS_PUBLISHED,
            'property_type' => 'Commercial Land',
            'state' => 'Lagos',
            'lga' => 'Ibeju-Lekki',
            'neighbourhood' => 'Eleko',
        ]);

        Testimonial::factory()->create([
            'client_name' => 'Alhaji Ibrahim Musa',
            'client_role_or_title' => 'Commercial Investor',
            'property_id' => $property->id,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.testimonials.index'));

        $response->assertOk();
        $response->assertSee('Alhaji Ibrahim Musa');
        $response->assertSee('Commercial Investor');
        $response->assertSee('LND-TEST-999');
    }

    public function test_admin_can_view_create_and_edit_testimonial_forms(): void
    {
        $category = PropertyCategory::factory()->create([
            'name' => 'Land',
            'slug' => 'land',
            'code_prefix' => 'LND',
        ]);

        $property = Property::create([
            'category_id' => $category->id,
            'reference' => 'LND-TEST-888',
            'title' => 'Sample Form Land',
            'slug' => 'sample-form-land',
            'description' => 'Test description',
            'price' => 20000000,
            'currency' => 'NGN',
            'status' => Property::STATUS_PUBLISHED,
            'property_type' => 'Residential Land',
            'state' => 'Lagos',
            'lga' => 'Eti-Osa',
            'neighbourhood' => 'Lekki',
        ]);

        $testimonial = Testimonial::factory()->create([
            'property_id' => $property->id,
        ]);

        $createRes = $this->actingAs($this->admin)->get(route('admin.testimonials.create'));
        $createRes->assertOk();
        $createRes->assertSee('LND-TEST-888');

        $editRes = $this->actingAs($this->admin)->get(route('admin.testimonials.edit', $testimonial));
        $editRes->assertOk();
        $editRes->assertSee('LND-TEST-888');
    }

    public function test_admin_can_create_client_testimonial(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.testimonials.store'), [
            'client_name' => 'Mrs. Folashade Adeyemi',
            'client_role_or_title' => 'Diaspora Buyer, UK',
            'client_location' => 'London, UK',
            'content' => 'Victor conducted thorough legal search on the 2 plots in Lekki. Truly transparent service.',
            'rating' => 5,
            'is_published' => 1,
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.testimonials.index'));
        $this->assertDatabaseHas('testimonials', [
            'client_name' => 'Mrs. Folashade Adeyemi',
            'rating' => 5,
            'is_published' => true,
        ]);
    }

    public function test_testimonial_validation_requires_name_and_content_and_rating(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.testimonials.store'), [
            'client_name' => '',
            'content' => '',
            'rating' => 6, // invalid > 5
        ]);

        $response->assertSessionHasErrors(['client_name', 'content', 'rating']);
    }

    public function test_admin_can_update_testimonial(): void
    {
        $testimonial = Testimonial::factory()->create([
            'client_name' => 'Chief Emeka Okonkwo',
            'rating' => 4,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.testimonials.update', $testimonial), [
            'client_name' => 'Chief Emeka Okonkwo (SAN)',
            'client_role_or_title' => 'Legal Practitioner',
            'content' => 'Outstanding property due diligence and title investigation in Abuja.',
            'rating' => 5,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.testimonials.index'));
        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'client_name' => 'Chief Emeka Okonkwo (SAN)',
            'rating' => 5,
        ]);
    }

    public function test_admin_can_toggle_publish_status(): void
    {
        $testimonial = Testimonial::factory()->create([
            'is_published' => false,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.testimonials.toggle-publish', $testimonial));

        $response->assertRedirect();
        $this->assertTrue($testimonial->fresh()->is_published);

        // Toggle back to false
        $this->actingAs($this->admin)->patch(route('admin.testimonials.toggle-publish', $testimonial));
        $this->assertFalse($testimonial->fresh()->is_published);
    }

    public function test_admin_can_delete_testimonial(): void
    {
        $testimonial = Testimonial::factory()->create([
            'client_name' => 'To Be Deleted',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.testimonials.destroy', $testimonial));

        $response->assertRedirect(route('admin.testimonials.index'));
        $this->assertDatabaseMissing('testimonials', [
            'id' => $testimonial->id,
        ]);
    }

    public function test_public_homepage_renders_only_published_testimonials(): void
    {
        $published = Testimonial::factory()->create([
            'client_name' => 'Visible Buyer Review',
            'content' => 'This review should be visible on the homepage.',
            'is_published' => true,
        ]);

        $unpublished = Testimonial::factory()->create([
            'client_name' => 'Secret Unpublished Review',
            'content' => 'This review must never be visible to the public.',
            'is_published' => false,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Visible Buyer Review');
        $response->assertSee('This review should be visible on the homepage.');
        $response->assertDontSee('Secret Unpublished Review');
        $response->assertDontSee('This review must never be visible to the public.');
    }
}

