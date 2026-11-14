<?php

use App\Models\Setting;

if (!function_exists('settings')) {
    /**
     * Retrieve a website or agency setting with safe default.
     */
    function settings(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

