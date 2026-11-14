<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            // General / Brand
            [
                'key' => 'agency_name',
                'value' => 'Victor Akande Real Estate',
                'group' => 'general',
            ],
            [
                'key' => 'agent_name',
                'value' => 'Victor Akande',
                'group' => 'general',
            ],
            [
                'key' => 'agent_title',
                'value' => 'Lead Independent Real Estate Advisor',
                'group' => 'general',
            ],

            // Contact Channels
            [
                'key' => 'phone',
                'value' => '+234 800 000 0000',
                'group' => 'contact',
            ],
            [
                'key' => 'whatsapp_number',
                'value' => '2348000000000',
                'group' => 'contact',
            ],
            [
                'key' => 'email',
                'value' => 'victor@example.com',
                'group' => 'contact',
            ],
            [
                'key' => 'office_address',
                'value' => 'Lekki Phase 1, Lagos State, Nigeria',
                'group' => 'contact',
            ],

            // Social Media
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/victorakande.realtor',
                'group' => 'social',
            ],
            [
                'key' => 'social_linkedin',
                'value' => 'https://linkedin.com/in/victorakande',
                'group' => 'social',
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://x.com/victorakande',
                'group' => 'social',
            ],
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/victorakanderealestate',
                'group' => 'social',
            ],

            // Homepage Hero & Content
            [
                'key' => 'homepage_headline',
                'value' => 'Secure Verified Land & Prime Properties Across Nigeria.',
                'group' => 'homepage',
            ],
            [
                'key' => 'homepage_subheadline',
                'value' => 'Partner with an independent real estate professional who prioritizes thorough legal due diligence, authentic documentation, and transparent property acquisition.',
                'group' => 'homepage',
            ],
            [
                'key' => 'about_summary',
                'value' => 'Navigating property acquisition in Nigeria requires transparency, title validation, and direct communication. As an independent real estate agent, I offer personalized guidance without multi-layered agency bureaucracy.',
                'group' => 'homepage',
            ],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }

        Setting::clearCache();
    }
}

