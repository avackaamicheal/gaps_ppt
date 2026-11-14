<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Brand & Agent Profile
            'agency_name' => ['required', 'string', 'max:255'],
            'agent_name' => ['required', 'string', 'max:255'],
            'agent_title' => ['nullable', 'string', 'max:255'],

            // Contact & Direct Channels
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp_number' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'office_address' => ['nullable', 'string', 'max:500'],

            // Homepage Hero & Narrative
            'homepage_headline' => ['nullable', 'string', 'max:500'],
            'homepage_subheadline' => ['nullable', 'string', 'max:1000'],
            'about_narrative' => ['nullable', 'string', 'max:5000'],

            // Social Media Profiles
            'social_instagram' => ['nullable', 'string', 'max:255'],
            'social_linkedin' => ['nullable', 'string', 'max:255'],
            'social_facebook' => ['nullable', 'string', 'max:255'],
            'social_twitter' => ['nullable', 'string', 'max:255'],

            // SEO Metadata
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}

