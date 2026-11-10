<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCustomerCrmTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_guest_cannot_access_customer_crm(): void
    {
        $response = $this->get('/admin/customers');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_customers_directory(): void
    {
        Customer::create([
            'name' => 'Chief Kolawole Williams',
            'phone' => '08033334444',
            'email' => 'kola@williams.ng',
            'source' => Customer::SOURCE_REFERRAL,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/customers');

        $response->assertOk();
        $response->assertSeeText('Chief Kolawole Williams');
        $response->assertSeeText('08033334444');
    }

    public function test_admin_can_search_customers_by_keyword(): void
    {
        Customer::create([
            'name' => 'Target Client',
            'phone' => '08011112222',
            'source' => Customer::SOURCE_WEBSITE,
        ]);

        Customer::create([
            'name' => 'Other Prospect',
            'phone' => '08099998888',
            'source' => Customer::SOURCE_WEBSITE,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/customers?search=Target');

        $response->assertOk();
        $response->assertSeeText('Target Client');
        $response->assertDontSeeText('Other Prospect');
    }

    public function test_admin_can_create_customer_profile(): void
    {
        $payload = [
            'name' => 'Madam Bimpe Ojo',
            'phone' => '08123456789',
            'email' => 'bimpe@example.ng',
            'source' => Customer::SOURCE_PHONE,
            'notes' => 'Looking for 2 plots in Epe for commercial poultry farming.',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/customers', $payload);

        $customer = Customer::where('phone', '08123456789')->first();
        $this->assertNotNull($customer);

        $response->assertRedirect(route('admin.customers.show', $customer));

        $this->assertDatabaseHas('customers', [
            'name' => 'Madam Bimpe Ojo',
            'phone' => '08123456789',
            'source' => Customer::SOURCE_PHONE,
        ]);
    }

    public function test_admin_can_view_customer_360_profile_with_linked_enquiries(): void
    {
        $customer = Customer::create([
            'name' => 'Governor Kayode',
            'phone' => '08088889999',
            'source' => Customer::SOURCE_OFFLINE,
        ]);

        Enquiry::create([
            'customer_id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'message' => 'Interested in waterfront plots.',
            'source' => Enquiry::SOURCE_OFFLINE,
            'status' => Enquiry::STATUS_NEW,
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/customers/{$customer->id}");

        $response->assertOk();
        $response->assertSeeText('Governor Kayode');
        $response->assertSeeText('Interested in waterfront plots.');
    }

    public function test_admin_can_update_customer_profile(): void
    {
        $customer = Customer::create([
            'name' => 'Original Name',
            'phone' => '08012340000',
            'source' => Customer::SOURCE_WEBSITE,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/customers/{$customer->id}", [
            'name' => 'Updated Full Name',
            'phone' => '08012340000',
            'email' => 'updated@example.com',
            'source' => Customer::SOURCE_REFERRAL,
            'notes' => 'Budget increased to ₦150M.',
        ]);

        $response->assertRedirect(route('admin.customers.show', $customer));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Full Name',
            'email' => 'updated@example.com',
            'notes' => 'Budget increased to ₦150M.',
        ]);
    }

    public function test_admin_can_soft_delete_customer(): void
    {
        $customer = Customer::create([
            'name' => 'Delete Me',
            'phone' => '08099990000',
            'source' => Customer::SOURCE_WEBSITE,
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/customers/{$customer->id}");

        $response->assertRedirect(route('admin.customers.index'));
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }
}
