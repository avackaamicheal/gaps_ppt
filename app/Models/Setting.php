<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Retrieve a setting value by key with safe fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $settings = Cache::rememberForever('app_settings_cache', function () {
                return static::all()->pluck('value', 'key')->toArray();
            });

            return $settings[$key] ?? $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set or update a setting value.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget('app_settings_cache');
    }

    /**
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('app_settings_cache');
    }
}

