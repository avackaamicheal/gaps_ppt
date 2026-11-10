<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

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
        'name',
        'phone',
        'email',
        'source',
        'notes',
    ];

    /**
     * Enquiries originating from this customer.
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class)->latest();
    }

    /**
     * Site inspection appointments booked for this customer.
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class)->latest('scheduled_date');
    }

    /**
     * Scope to search by name, phone or email.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    /**
     * Normalize a phone number for indexing and deduplication.
     */
    public static function normalizePhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Strip spaces, dashes, parentheses
        $cleaned = preg_replace('/[^0-9+]/', '', trim($phone));

        // If local Nigerian format (e.g. 08012345678), standardize to 234 format without +
        if (str_starts_with($cleaned, '0') && strlen($cleaned) === 11) {
            return '234' . substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '+')) {
            return substr($cleaned, 1);
        }

        return $cleaned;
    }
}

