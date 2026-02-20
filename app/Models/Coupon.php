<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'partner_business_id',
        'title',
        'description',
        'discount_value',
        'discount_type',
        'max_redemptions',
        'used_count',
        'expiry_date',
        'start_time',
        'end_time',
        'radius_km',
        'first_time_only',
        'is_boosted',
        'boosted_until',
        'featured_flag',
        'priority_score',
        'view_count',
        'status',
        'quality_score',
        'complaint_count',
        'rating_sum',
        'rating_count',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'boosted_until' => 'datetime',
            'first_time_only' => 'boolean',
            'is_boosted' => 'boolean',
            'featured_flag' => 'boolean',
            'radius_km' => 'decimal:2',
            'discount_value' => 'decimal:2',
        ];
    }

    public const CATEGORIES = [
        'food' => 'Food & Dining',
        'salon' => 'Salon & Beauty',
        'health' => 'Health & Wellness',
        'services' => 'Services',
        'shopping' => 'Shopping',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function partnerBusiness()
    {
        return $this->belongsTo(Business::class, 'partner_business_id');
    }

    public function isJointCoupon(): bool
    {
        return (bool) $this->partner_business_id;
    }

    public function redemptions()
    {
        return $this->hasMany(Redemption::class);
    }

    public function clicks()
    {
        return $this->hasMany(CouponClick::class, 'coupon_id');
    }

    public function isExpired(): bool
    {
        if ($this->expiry_date->isPast()) {
            return true;
        }
        if ($this->used_count >= $this->max_redemptions) {
            return true;
        }
        return false;
    }

    public function isWithinTime(): bool
    {
        if (!$this->start_time || !$this->end_time) {
            return true;
        }
        $now = now()->format('H:i:s');
        $start = $this->start_time instanceof \Carbon\Carbon ? $this->start_time->format('H:i:s') : (string) $this->start_time;
        $end = $this->end_time instanceof \Carbon\Carbon ? $this->end_time->format('H:i:s') : (string) $this->end_time;
        return $now >= $start && $now <= $end;
    }

    public function getFormattedDiscountAttribute(): string
    {
        return $this->discount_type === 'percentage'
            ? "{$this->discount_value}% OFF"
            : "₹{$this->discount_value} OFF";
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expiry_date', '>=', now()->toDateString())
            ->whereColumn('used_count', '<', 'max_redemptions');
    }

    public function scopeBoosted($query)
    {
        return $query->where('is_boosted', true)
            ->where(function ($q) {
                $q->whereNull('boosted_until')->orWhere('boosted_until', '>', now());
            });
    }
}
