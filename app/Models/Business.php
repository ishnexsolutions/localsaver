<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'address',
        'lat',
        'lng',
        'radius_km',
        'verified',
        'activated',
        'activated_at',
        'popularity_score',
        'onboarding_completed',
    ];

    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
            'activated' => 'boolean',
            'activated_at' => 'datetime',
            'onboarding_completed' => 'boolean',
            'lat' => 'decimal:8',
            'lng' => 'decimal:8',
            'radius_km' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function activeCoupons()
    {
        return $this->hasMany(Coupon::class)->where('status', 'active')
            ->where('expiry_date', '>=', now()->toDateString());
    }

    public function partners()
    {
        return $this->belongsToMany(Business::class, 'business_partners', 'business_id', 'partner_business_id')
            ->withTimestamps();
    }

    public function partnerRecords()
    {
        return $this->hasMany(BusinessPartner::class);
    }

    public function partnershipRequestsSent()
    {
        return $this->hasMany(PartnershipRequest::class, 'requester_business_id');
    }

    public function partnershipRequestsReceived()
    {
        return $this->hasMany(PartnershipRequest::class, 'target_business_id');
    }

    public function couponsAsPrimary()
    {
        return $this->hasMany(Coupon::class, 'business_id');
    }

    public function couponsAsPartner()
    {
        return $this->hasMany(Coupon::class, 'partner_business_id');
    }

    public function ratings()
    {
        return $this->hasMany(BusinessRating::class);
    }

    public function trustScore()
    {
        return $this->hasOne(BusinessTrustScore::class);
    }
}
