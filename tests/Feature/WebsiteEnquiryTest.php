<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class WebsiteEnquiryTest extends TestCase
{
    use RefreshDatabase;

    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('enquiry:127.0.0.1');

        $category = PropertyCategory::create([
            'name' => 'Land',
            'slug' => 'land',
            'code_prefix' => 'LAND',
            'is_active' => true,
        ]);

        $this->property = Property::create([
            'category_id' => $category->id,
            'title' => 'Prime Commercial Plot in Lekki',
            'slug' => 'prime-commercial-plot-in-lekki',
            'reference' => 'LAND-0001',
            'price' => 85000000,
            'status' => Property::STATUS_PUBLISHED,
            'state' => 'Lagos',
            'lga' => 'Eti-Osa',
            'neighbourhood' => 'Lekki Phase 1',
            'description' => 'Great dry land suitable for development.',
            'published_at' => now(),
        ]);
    }

    public function test_visitor_can_submit_enquiry_for_published_property(): void
    {
        $response = $this->post('/enquiries', [
            'property_id' => $this->property->id,
            'name' => 'Chief Adeleke Babatunde',
            'phone' => '08031234567',
            'email' => 'adeleke@example.ng',
            'message' => 'I would like to know if the title is Governor Consent and schedule an inspection this Friday.',
            'bot_check' => '',
        ]);

        $response->assertSessionHas('enquiry_success');

        // Customer profile created
        $this->assertDatabaseHas('customers', [
            'name' => 'Chief Adeleke Babatunde',
            'phone' => '08031234567',
            'email' => 'adeleke@example.ng',
            'source' => Customer::SOURCE_WEBSITE,
        ]);

        $customer = Customer::where('phone', '08031234567')->first();

        // Enquiry created and linked
        $this->assertDatabaseHas('enquiries', [
            'customer_id' => $customer->id,
            'property_id' => $this->property->id,
            'name' => 'Chief Adeleke Babatunde',
            'phone' => '08031234567',
            'email' => 'adeleke@example.ng',
            'source' => Enquiry::SOURCE_WEBSITE,
            'status' => Enquiry::STATUS_NEW,
        ]);
    }

    public function test_subsequent_enquiry_from_same_phone_reuses_existing_customer(): void
    {
        // First enquiry
        $this->post('/enquiries', [
            'property_id' => $this->property->id,
            'name' => 'Alhaji Dangote',
            'phone' => '08099887766',
            'message' => 'First message.',
            'bot_check' => '',
        ]);

        $this->assertEquals(1, Customer::count());
        $initialCustomer = Customer::first();

        // Second enquiry from same person (normalized phone format)
        $this->post('/enquiries', [
            'property_id' => $this->property->id,
            'name' => 'Alhaji Dangote',
            'phone' => '+234 809 988 7766',
            'email' => 'dangote@example.ng',
            'message' => 'Second enquiry about another plot.',
            'bot_check' => '',
        ]);

        // Customer count must remain 1 (deduplicated)
        $this->assertEquals(1, Customer::count());
        $this->assertEquals(2, Enquiry::count());

        $this->assertEquals($initialCustomer->id, Enquiry::latest()->first()->customer_id);
    }

    public function test_honeypot_silently_drops_bot_submission(): void
    {
        $response = $this->post('/enquiries', [
            'property_id' => $this->property->id,
            'name' => 'Spam Bot 3000',
            'phone' => '1234567890',
            'message' => 'Buy cheap pills at spam.com',
            'bot_check' => 'I am a spam bot filling hidden inputs',
        ]);

        // Silently redirects back pretending success
        $response->assertSessionHas('enquiry_success');

        // Neither customer nor enquiry is saved
        $this->assertDatabaseCount('customers', 0);
        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_enquiry_requires_mandatory_fields(): void
    {
        $response = $this->post('/enquiries', [
            'property_id' => $this->property->id,
            'name' => '',
            'phone' => '',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'phone', 'message']);
        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_rate_limiter_throttles_excessive_submissions(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/enquiries', [
                'property_id' => $this->property->id,
                'name' => "User {$i}",
                'phone' => "0801000000{$i}",
                'message' => "Legitimate message number {$i}",
                'bot_check' => '',
            ]);
        }

        // 6th attempt within the same minute from the same IP
        $response = $this->post('/enquiries', [
            'property_id' => $this->property->id,
            'name' => 'Excessive Submitter',
            'phone' => '08099999999',
            'message' => 'This should be throttled',
            'bot_check' => '',
        ]);

        $response->assertSessionHasErrors('enquiry_throttle');
        $this->assertEquals(5, Enquiry::count());
    }
}

