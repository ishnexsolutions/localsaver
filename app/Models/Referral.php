<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'vip_unlocked',
        'referrer_reward_claimed',
        'referred_reward_claimed',
        'referred_redemption_count',
    ];

    protected function casts(): array
    {
        return [
            'vip_unlocked' => 'boolean',
            'referrer_reward_claimed' => 'boolean',
            'referred_reward_claimed' => 'boolean',
        ];
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }
}
