<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_guest_cannot_access_activity_logs(): void
    {
        $response = $this->get(route('admin.activity-logs.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_activity_logs(): void
    {
        ActivityLogger::log('property.test_action', 'Test administrative event recorded');

        $response = $this->actingAs($this->admin)->get(route('admin.activity-logs.index'));

        $response->assertOk();
        $response->assertSee('Test administrative event recorded');
        $response->assertSee('property.test_action');
    }

    public function test_activity_logger_sanitizes_sensitive_keys(): void
    {
        $log = ActivityLogger::log('user.auth_test', 'Sensitive payload test', null, [
            'safe_param' => 'visible_value',
            'password' => 'super_secret_password',
            'token' => 'bearer_token_123',
            'bot_check' => 'spam_honeypot_trap',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'action' => 'user.auth_test',
        ]);

        $storedProperties = $log->fresh()->properties;
        $this->assertArrayHasKey('safe_param', $storedProperties);
        $this->assertArrayNotHasKey('password', $storedProperties);
        $this->assertArrayNotHasKey('token', $storedProperties);
        $this->assertArrayNotHasKey('bot_check', $storedProperties);
    }

    public function test_property_status_change_creates_activity_log(): void
    {
        $category = PropertyCategory::factory()->create([
            'name' => 'Land',
            'slug' => 'land',
            'code_prefix' => 'LND',
        ]);

        $property = Property::create([
            'category_id' => $category->id,
            'reference' => 'LND-TEST-001',
            'title' => 'Test Commercial Acreage',
            'slug' => 'test-commercial-acreage',
            'description' => 'Test description',
            'price' => 50000000,
            'currency' => 'NGN',
            'status' => Property::STATUS_DRAFT,
            'property_type' => 'Commercial Land',
            'state' => 'Lagos',
            'lga' => 'Ibeju-Lekki',
            'neighbourhood' => 'Eleko',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.properties.status', $property), [
            'status' => Property::STATUS_PUBLISHED,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'property.status_updated',
            'subject_id' => $property->id,
            'subject_type' => Property::class,
        ]);
    }
}
