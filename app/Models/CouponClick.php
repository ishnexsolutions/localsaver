<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponClick extends Model
{
    protected $fillable = [
        'coupon_id',
        'user_id',
        'ip_hash',
        'session_id',
        'is_unique_user',
        'converted_to_redemption',
    ];

    protected function casts(): array
    {
        return [
            'is_unique_user' => 'boolean',
            'converted_to_redemption' => 'boolean',
        ];
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
