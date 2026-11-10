<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enquiry extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_INSPECTION_SCHEDULED = 'inspection_scheduled';
    public const STATUS_NEGOTIATION = 'negotiation';
    public const STATUS_CONVERTED = 'converted';
    public const STATUS_CLOSED_LOST = 'closed_lost';

    public const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_CONTACTED => 'Contacted',
        self::STATUS_INSPECTION_SCHEDULED => 'Inspection Scheduled',
        self::STATUS_NEGOTIATION => 'Under Negotiation',
        self::STATUS_CONVERTED => 'Converted / Sold',
        self::STATUS_CLOSED_LOST => 'Closed / Lost',
    ];

    public const SOURCE_WEBSITE = 'website';
    public const SOURCE_PHONE = 'phone';
    public const SOURCE_WHATSAPP = 'whatsapp';
    public const SOURCE_REFERRAL = 'referral';
    public const SOURCE_OFFLINE = 'offline';

    public const SOURCES = [
        self::SOURCE_WEBSITE => 'Website',
        self::SOURCE_WHATSAPP => 'WhatsApp',
        self::SOURCE_PHONE => 'Phone Call',
        self::SOURCE_REFERRAL => 'Referral',
        self::SOURCE_OFFLINE => 'Offline Walk-in',
    ];

    protected $fillable = [
        'customer_id',
        'property_id',
        'name',
        'phone',
        'email',
        'message',
        'source',
        'status',
        'internal_notes',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function inspection(): HasOne
    {
        return $this->hasOne(Inspection::class);
    }

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (!empty($filters['property_id'])) {
            $query->where('property_id', $filters['property_id']);
        }

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('message', 'like', "%{$term}%");
            });
        }

        return $query;
    }
}

