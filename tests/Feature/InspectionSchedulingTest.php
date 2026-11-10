<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Inspection;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InspectionSchedulingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Property $property;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $category = PropertyCategory::create([
            'name' => 'Commercial',
            'slug' => 'commercial',
            'code_prefix' => 'COMM',
            'is_active' => true,
        ]);

        $this->property = Property::create([
            'category_id' => $category->id,
            'title' => 'Plaza along Lekki-Epe Expressway',
            'slug' => 'plaza-along-lekki-epe-expressway',
            'reference' => 'COMM-0001',
            'price' => 320000000,
            'status' => Property::STATUS_PUBLISHED,
            'state' => 'Lagos',
            'lga' => 'Ibeju-Lekki',
            'neighbourhood' => 'Eleko',
            'description' => 'Great commercial investment.',
        ]);

        $this->customer = Customer::create([
            'name' => 'Alhaji Bashir Tofa',
            'phone' => '08034445555',
            'email' => 'bashir@tofa.ng',
            'source' => Customer::SOURCE_PHONE,
        ]);
    }

    public function test_guest_cannot_access_inspections(): void
    {
        $response = $this->get('/admin/inspections');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_schedule_site_inspection(): void
    {
        $payload = [
            'property_id' => $this->property->id,
            'customer_id' => $this->customer->id,
            'scheduled_date' => now()->addDays(2)->toDateString(),
            'scheduled_time' => '11:00 AM',
            'meeting_location' => 'Eleko Beach Junction Total Petrol Station',
            'status' => Inspection::STATUS_CONFIRMED,
            'notes' => 'Client will arrive with personal surveyor.',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/inspections', $payload);

        $inspection = Inspection::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($inspection);

        $response->assertRedirect(route('admin.inspections.show', $inspection));

        $this->assertDatabaseHas('inspections', [
            'id' => $inspection->id,
            'property_id' => $this->property->id,
            'meeting_location' => 'Eleko Beach Junction Total Petrol Station',
            'status' => Inspection::STATUS_CONFIRMED,
        ]);
    }

    public function test_scheduling_inspection_from_enquiry_updates_enquiry_status(): void
    {
        $enquiry = Enquiry::create([
            'customer_id' => $this->customer->id,
            'property_id' => $this->property->id,
            'name' => $this->customer->name,
            'phone' => $this->customer->phone,
            'message' => 'Want to see the plaza this weekend.',
            'source' => Enquiry::SOURCE_WEBSITE,
            'status' => Enquiry::STATUS_NEW,
        ]);

        $payload = [
            'property_id' => $this->property->id,
            'customer_id' => $this->customer->id,
            'enquiry_id' => $enquiry->id,
            'scheduled_date' => now()->addDay()->toDateString(),
            'scheduled_time' => '10:00 AM',
            'meeting_location' => 'Directly at Plaza Gate',
            'status' => Inspection::STATUS_CONFIRMED,
        ];

        $this->actingAs($this->admin)->post('/admin/inspections', $payload);

        $enquiry->refresh();
        $this->assertEquals(Enquiry::STATUS_INSPECTION_SCHEDULED, $enquiry->status);
    }

    public function test_admin_can_update_inspection_status(): void
    {
        $inspection = Inspection::create([
            'property_id' => $this->property->id,
            'customer_id' => $this->customer->id,
            'scheduled_date' => now()->toDateString(),
            'scheduled_time' => '02:00 PM',
            'meeting_location' => 'Plaza Gate',
            'status' => Inspection::STATUS_CONFIRMED,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/inspections/{$inspection->id}", [
            'property_id' => $this->property->id,
            'customer_id' => $this->customer->id,
            'scheduled_date' => $inspection->scheduled_date->toDateString(),
            'scheduled_time' => '02:00 PM',
            'meeting_location' => 'Plaza Gate',
            'status' => Inspection::STATUS_COMPLETED,
            'notes' => 'Client inspected and made an offer of ₦300M.',
        ]);

        $response->assertRedirect(route('admin.inspections.show', $inspection));

        $this->assertDatabaseHas('inspections', [
            'id' => $inspection->id,
            'status' => Inspection::STATUS_COMPLETED,
            'notes' => 'Client inspected and made an offer of ₦300M.',
        ]);
    }

    public function test_scopes_isolate_upcoming_and_past_inspections(): void
    {
        // Future inspection
        $upcoming = Inspection::create([
            'property_id' => $this->property->id,
            'customer_id' => $this->customer->id,
            'scheduled_date' => now()->addDays(3)->toDateString(),
            'scheduled_time' => '10:00 AM',
            'meeting_location' => 'Future Meeting Point',
            'status' => Inspection::STATUS_CONFIRMED,
        ]);

        // Past inspection
        $past = Inspection::create([
            'property_id' => $this->property->id,
            'customer_id' => $this->customer->id,
            'scheduled_date' => now()->subDays(5)->toDateString(),
            'scheduled_time' => '10:00 AM',
            'meeting_location' => 'Past Meeting Point',
            'status' => Inspection::STATUS_COMPLETED,
        ]);

        $this->assertTrue(Inspection::upcoming()->pluck('id')->contains($upcoming->id));
        $this->assertFalse(Inspection::upcoming()->pluck('id')->contains($past->id));

        $this->assertTrue(Inspection::past()->pluck('id')->contains($past->id));
        $this->assertFalse(Inspection::past()->pluck('id')->contains($upcoming->id));
    }

    public function test_dashboard_displays_live_phase_3_metrics(): void
    {
        Enquiry::create([
            'customer_id' => $this->customer->id,
            'property_id' => $this->property->id,
            'name' => 'New Lead',
            'phone' => '08099990000',
            'message' => 'Hello Victor',
            'source' => Enquiry::SOURCE_WEBSITE,
            'status' => Enquiry::STATUS_NEW,
        ]);

        Inspection::create([
            'property_id' => $this->property->id,
            'customer_id' => $this->customer->id,
            'scheduled_date' => now()->addDays(1)->toDateString(),
            'scheduled_time' => '12:00 PM',
            'meeting_location' => 'Junction',
            'status' => Inspection::STATUS_CONFIRMED,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSeeText('New Leads');
        $response->assertSeeText('Recent Inbound Leads');
        $response->assertSeeText('Upcoming Inspections');
    }
}
