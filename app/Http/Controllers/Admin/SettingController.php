<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Map setting keys to their logical category group.
     */
    protected array $groupMap = [
        'agency_name' => 'profile',
        'agent_name' => 'profile',
        'agent_title' => 'profile',
        'phone' => 'contact',
        'whatsapp_number' => 'contact',
        'email' => 'contact',
        'office_address' => 'contact',
        'homepage_headline' => 'content',
        'homepage_subheadline' => 'content',
        'about_narrative' => 'content',
        'social_instagram' => 'social',
        'social_linkedin' => 'social',
        'social_facebook' => 'social',
        'social_twitter' => 'social',
        'meta_title' => 'seo',
        'meta_description' => 'seo',
    ];

    public function edit(): View
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        foreach ($data as $key => $value) {
            $group = $this->groupMap[$key] ?? 'general';
            Setting::set($key, $value, $group);
        }

        ActivityLogger::log(
            'settings.updated',
            'Updated website branding, contact information, and homepage content'
        );

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Website settings have been updated successfully.');
    }
}

