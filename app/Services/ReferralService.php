<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\User;

class ReferralService
{
    public function registerReferral(User $newUser, string $referralCode): ?Referral
    {
        $referrer = User::where('referral_code', $referralCode)->where('id', '!=', $newUser->id)->first();
        if (!$referrer || $newUser->referred_by) {
            return null;
        }
        if (Referral::where('referred_id', $newUser->id)->exists()) {
            return null;
        }

        $referral = Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
        ]);

        $newUser->update(['referred_by' => $referrer->id]);
        $referrer->increment('referral_count');

        return $referral;
    }

    public function onReferredUserRedemption(User $user): void
    {
        $referral = Referral::where('referred_id', $user->id)->first();
        if (!$referral) {
            return;
        }

        $referral->increment('referred_redemption_count');

        if ($referral->referred_redemption_count >= 3 && !$referral->vip_unlocked) {
            $referral->update(['vip_unlocked' => true]);
            $user->update(['vip_unlocked' => true]);
        }
    }
}
