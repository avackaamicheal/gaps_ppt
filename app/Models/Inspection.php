<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inspection extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending_confirmation';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_RESCHEDULED = 'rescheduled';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending Confirmation',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_RESCHEDULED => 'Rescheduled',
    ];

    protected $fillable = [
        'property_id',
        'customer_id',
        'enquiry_id',
        'scheduled_date',
        'scheduled_time',
        'meeting_location',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', now()->toDateString())
                     ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_RESCHEDULED])
                     ->orderBy('scheduled_date', 'asc');
    }

    public function scopePast($query)
    {
        return $query->where(function ($q) {
            $q->where('scheduled_date', '<', now()->toDateString())
              ->orWhereIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
        })->orderBy('scheduled_date', 'desc');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->scheduled_date ? $this->scheduled_date->format('D, M j, Y') : '';
    }

    public function getIsPastAttribute(): bool
    {
        if (!$this->scheduled_date) return false;
        return $this->scheduled_date->isPast();
    }
}

