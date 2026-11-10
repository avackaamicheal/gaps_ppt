<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEnquiryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $category = PropertyCategory::create([
            'name' => 'Residential',
            'slug' => 'residential',
            'code_prefix' => 'PROP',
            'is_active' => true,
        ]);

        $this->property = Property::create([
            'category_id' => $category->id,
            'title' => 'Semi-Detached Duplex in Ikoyi',
            'slug' => 'semi-detached-duplex-in-ikoyi',
            'reference' => 'PROP-0001',
            'price' => 250000000,
            'status' => Property::STATUS_PUBLISHED,
            'state' => 'Lagos',
            'lga' => 'Eti-Osa',
            'neighbourhood' => 'Ikoyi',
            'description' => 'Luxury home.',
        ]);
    }

    public function test_guest_cannot_access_admin_enquiries(): void
    {
        $response = $this->get('/admin/enquiries');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_enquiries_index(): void
    {
        Enquiry::create([
            'name' => 'Senator Victor Ndoma',
            'phone' => '08022223333',
            'message' => 'Interested in Ikoyi duplex.',
            'source' => Enquiry::SOURCE_WEBSITE,
            'status' => Enquiry::STATUS_NEW,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/enquiries');

        $response->assertOk();
        $response->assertSeeText('Senator Victor Ndoma');
    }

    public function test_admin_can_record_offline_lead(): void
    {
        $payload = [
            'name' => 'Mrs. Folake Adeyemi',
            'phone' => '08051234567',
            'email' => 'folake@example.com',
            'source' => Enquiry::SOURCE_WHATSAPP,
            'status' => Enquiry::STATUS_CONTACTED,
            'property_id' => $this->property->id,
            'message' => 'Inquired on WhatsApp regarding payment installment plans.',
            'internal_notes' => 'Very interested. Proposed 2 payments over 60 days.',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/enquiries', $payload);

        $enquiry = Enquiry::where('phone', '08051234567')->first();
        $this->assertNotNull($enquiry);

        $response->assertRedirect(route('admin.enquiries.show', $enquiry));

        $this->assertEquals(Enquiry::SOURCE_WHATSAPP, $enquiry->source);
        $this->assertEquals(Enquiry::STATUS_CONTACTED, $enquiry->status);
        $this->assertDatabaseHas('customers', [
            'name' => 'Mrs. Folake Adeyemi',
            'phone' => '08051234567',
        ]);
    }

    public function test_admin_can_update_enquiry_status_and_notes(): void
    {
        $enquiry = Enquiry::create([
            'name' => 'Engr. David Mark',
            'phone' => '08077778888',
            'message' => 'Available for inspection tomorrow.',
            'source' => Enquiry::SOURCE_PHONE,
            'status' => Enquiry::STATUS_NEW,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/enquiries/{$enquiry->id}", [
            'status' => Enquiry::STATUS_INSPECTION_SCHEDULED,
            'internal_notes' => 'Confirmed inspection for Saturday 11am.',
        ]);

        $response->assertSessionHas('status');

        $this->assertDatabaseHas('enquiries', [
            'id' => $enquiry->id,
            'status' => Enquiry::STATUS_INSPECTION_SCHEDULED,
            'internal_notes' => 'Confirmed inspection for Saturday 11am.',
        ]);
    }

    public function test_admin_can_delete_enquiry(): void
    {
        $enquiry = Enquiry::create([
            'name' => 'Spam Test',
            'phone' => '0000000000',
            'message' => 'Spam content to remove.',
            'source' => Enquiry::SOURCE_WEBSITE,
            'status' => Enquiry::STATUS_CLOSED_LOST,
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/enquiries/{$enquiry->id}");

        $response->assertRedirect(route('admin.enquiries.index'));
        $this->assertDatabaseMissing('enquiries', ['id' => $enquiry->id]);
    }
}
