<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'title',
        'document_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Get the property that owns this document.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }
}

