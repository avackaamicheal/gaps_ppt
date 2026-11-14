<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'client_role_or_title',
        'client_location',
        'content',
        'rating',
        'property_id',
        'avatar_path',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'rating' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Optional property associated with this testimonial.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Scope to published testimonials only.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->orderBy('sort_order', 'asc')
                     ->latest('created_at');
    }

    /**
     * Get visual star representation.
     */
    public function getStarsAttribute(): string
    {
        return str_repeat('★', max(1, min(5, $this->rating))) . str_repeat('☆', max(0, 5 - $this->rating));
    }
}

