<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * Log an administrative activity event.
     */
    public static function log(string $action, string $description, ?Model $subject = null, array $properties = []): ActivityLog
    {
        // Sanitize properties to prevent logging passwords or secrets
        $sanitizedProperties = collect($properties)->except([
            'password',
            'password_confirmation',
            'token',
            'remember_token',
            'bot_check',
            'api_key',
            'secret',
        ])->toArray();

        return ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'properties' => !empty($sanitizedProperties) ? $sanitizedProperties : null,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}

