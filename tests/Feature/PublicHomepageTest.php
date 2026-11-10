<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicHomepageTest extends TestCase
{
    public function test_public_homepage_loads_successfully_without_authentication(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Victor Akande');
        $response->assertSeeText('Independent Real Estate Consultant');
    }

    public function test_public_homepage_contains_branding_and_cta_elements(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Secure Verified Land & Prime Properties Across Nigeria');
        $response->assertSeeText('+234 800 000 0000');
        $response->assertSee('https://wa.me/2348000000000', false);
    }

    public function test_public_homepage_contains_property_showcase_shell(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Land & Building Portfolio');
        $response->assertSeeText('Category: Land');
        $response->assertSeeText('Category: Buildings');
        $response->assertSeeText('Category: Commercial');
    }

    public function test_public_homepage_contains_about_section(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Personal Integrity, Thorough Due Diligence');
        $response->assertSeeText('Verified Documentation');
    }
}
