<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'suspended',
        'lat',
        'lng',
        'total_saved',
        'redemption_count',
        'last_active_at',
        'redemption_streak_weeks',
        'referral_code',
        'referred_by',
        'vip_unlocked',
        'referral_count',
        'milestones_notified',
        'onboarding_completed',
        'preferred_categories',
        'founding_member',
        'loyalty_streak_days',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_active_at' => 'datetime',
            'milestones_notified' => 'array',
            'onboarding_completed' => 'boolean',
            'preferred_categories' => 'array',
            'password' => 'hashed',
            'lat' => 'decimal:8',
            'lng' => 'decimal:8',
            'total_saved' => 'decimal:2',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBusiness(): bool
    {
        return $this->role === 'business';
    }

    public function isVip(): bool
    {
        return (bool) ($this->vip_unlocked ?? false) || $this->redemption_count >= 3;
    }

    public function business()
    {
        return $this->hasOne(Business::class);
    }

    public function redemptions()
    {
        return $this->hasMany(Redemption::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referralRecords()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function rewards()
    {
        return $this->hasMany(UserReward::class);
    }

    protected static function booted()
    {
        static::creating(function (User $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = strtoupper(substr(uniqid(), -6));
            }
        });
    }
}
