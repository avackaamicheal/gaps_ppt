<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_UNDER_NEGOTIATION = 'under_negotiation';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_SOLD = 'sold';
    public const STATUS_UNAVAILABLE = 'unavailable';
    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_UNDER_NEGOTIATION => 'Under Negotiation',
        self::STATUS_RESERVED => 'Reserved',
        self::STATUS_SOLD => 'Sold',
        self::STATUS_UNAVAILABLE => 'Unavailable',
        self::STATUS_ARCHIVED => 'Archived',
    ];

    protected $fillable = [
        'category_id',
        'reference',
        'title',
        'slug',
        'description',
        'price',
        'currency',
        'status',
        'property_type',
        'is_featured',
        'published_at',
        'state',
        'lga',
        'neighbourhood',
        'street_address',
        'plot_size',
        'plot_dimensions',
        'number_of_plots',
        'land_use',
        'title_type',
        'topography',
        'has_survey',
        'bedrooms',
        'bathrooms',
        'toilets',
        'parking_spaces',
        'floors',
        'building_condition',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'number_of_plots' => 'decimal:2',
        'is_featured' => 'boolean',
        'has_survey' => 'boolean',
        'published_at' => 'datetime',
        'features' => 'array',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'toilets' => 'integer',
        'parking_spaces' => 'integer',
        'floors' => 'integer',
    ];

    /**
     * Get the category for the property.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PropertyCategory::class, 'category_id');
    }

    /**
     * Get all images for the property.
     */
    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    /**
     * Get the cover image for the property.
     */
    public function coverImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->where('is_cover', true);
    }

    /**
     * Get all documents for the property.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(PropertyDocument::class);
    }

    /**
     * Get only approved public documents.
     */
    public function publicDocuments(): HasMany
    {
        return $this->hasMany(PropertyDocument::class)->where('is_public', true);
    }

    /**
     * Scope to only published properties.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope to featured properties.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to apply user filters.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        // Category filter (slug or id)
        if (!empty($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('slug', $filters['category'])
                  ->orWhere('id', $filters['category']);
            });
        }

        // Subtype / Property type
        if (!empty($filters['property_type'])) {
            $query->where('property_type', $filters['property_type']);
        }

        // State filter
        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        // LGA filter
        if (!empty($filters['lga'])) {
            $query->where('lga', $filters['lga']);
        }

        // Neighbourhood filter
        if (!empty($filters['neighbourhood'])) {
            $query->where('neighbourhood', $filters['neighbourhood']);
        }

        // Min price
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        // Max price
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        // Keyword search
        if (!empty($filters['keyword'])) {
            $keyword = '%' . trim($filters['keyword']) . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', $keyword)
                  ->orWhere('reference', 'LIKE', $keyword)
                  ->orWhere('description', 'LIKE', $keyword)
                  ->orWhere('neighbourhood', 'LIKE', $keyword)
                  ->orWhere('lga', 'LIKE', $keyword);
            });
        }

        return $query;
    }

    /**
     * Formatted price attribute (e.g. ₦45,000,000).
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₦' . number_format($this->price, 0);
    }

    /**
     * Display location string.
     */
    public function getDisplayLocationAttribute(): string
    {
        return "{$this->neighbourhood}, {$this->lga}, {$this->state}";
    }

    /**
     * Get cover image URL or fallback to first image.
     */
    public function getCoverUrlAttribute(): ?string
    {
        $cover = $this->images->firstWhere('is_cover', true) ?? $this->images->first();

        return $cover ? $cover->url : null;
    }

    /**
     * Check if property is in Land category.
     */
    public function isLand(): bool
    {
        return $this->category && $this->category->slug === 'land';
    }

    /**
     * Check if property is a Building.
     */
    public function isBuilding(): bool
    {
        return !$this->isLand();
    }

    /**
     * Enquiries received for this property.
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class)->latest();
    }

    /**
     * Physical site inspections scheduled for this property.
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class)->latest('scheduled_date');
    }
}

