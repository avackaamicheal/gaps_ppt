<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_guest_cannot_access_settings(): void
    {
        $response = $this->get(route('admin.settings.edit'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_settings_edit_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.edit'));

        $response->assertOk();
        $response->assertSee('Website Settings');
        $response->assertSee('Brand & Profile', false);
    }

    public function test_admin_can_update_website_settings(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.settings.update'), [
            'agency_name' => 'Akande Luxury Realty Ltd',
            'agent_name' => 'Victor Akande',
            'agent_title' => 'Senior Land & Commercial Advisor',
            'phone' => '+234 812 345 6789',
            'whatsapp_number' => '2348123456789',
            'email' => 'advisory@akandeluxury.com',
            'office_address' => 'Victoria Island, Lagos, Nigeria',
            'homepage_headline' => 'Exclusive Land Acquisitions In Nigeria',
            'homepage_subheadline' => 'Unlocking verified, institutional-grade land deals across Lagos.',
            'about_narrative' => 'Specializing in authenticated titles and zero-middlemen acquisitions.',
            'social_instagram' => 'https://instagram.com/akandeluxury',
            'social_linkedin' => 'https://linkedin.com/in/victorakande',
            'social_facebook' => '',
            'social_twitter' => '',
            'meta_title' => 'Akande Luxury Realty — Verified Properties',
            'meta_description' => 'Secure verified Nigerian land and building properties with confidence.',
        ]);

        $response->assertRedirect(route('admin.settings.edit'));
        $response->assertSessionHas('success');

        $this->assertEquals('Akande Luxury Realty Ltd', Setting::get('agency_name'));
        $this->assertEquals('+234 812 345 6789', Setting::get('phone'));
        $this->assertEquals('2348123456789', Setting::get('whatsapp_number'));
    }

    public function test_public_pages_reflect_updated_settings_dynamically(): void
    {
        Setting::set('agency_name', 'Prestige Properties Nigeria', 'profile');
        Setting::set('phone', '+234 809 999 8888', 'contact');
        Setting::set('homepage_headline', 'Verified Prime Land in Lekki & Epe', 'content');

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Prestige Properties Nigeria');
        $response->assertSee('+234 809 999 8888');
        $response->assertSee('Verified Prime Land in Lekki & Epe');
    }

    public function test_settings_helper_returns_default_when_key_does_not_exist(): void
    {
        $this->assertEquals('Default Value', settings('non_existent_key', 'Default Value'));
        $this->assertNull(settings('completely_unknown_key'));
    }
}

