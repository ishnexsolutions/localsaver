<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

class CouponPolicy
{
    public function redeem(User $user, Coupon $coupon): bool
    {
        return $user->role === 'user';
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $user->business && $user->business->id === $coupon->business_id;
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->business && $user->business->id === $coupon->business_id;
    }
}
