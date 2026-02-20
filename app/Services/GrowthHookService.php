<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserReward;

class GrowthHookService
{
    public static function onRedemption(User $user, $coupon): void
    {
        if ($user->redemption_count >= 5) {
            UserReward::firstOrCreate(
                ['user_id' => $user->id, 'type' => 'redeem_5'],
                ['claimed' => false]
            );
        }

        if ($user->redemption_count >= 1 && !$user->founding_member) {
            $totalUsers = \App\Models\User::where('role', 'user')->count();
            if ($totalUsers <= 1000) {
                $user->update(['founding_member' => true]);
            }
        }
    }

    public static function onShare(User $user): ?array
    {
        $reward = UserReward::firstOrCreate(
            ['user_id' => $user->id, 'type' => 'share_unlock'],
            ['claimed' => false]
        );
        if (!$reward->claimed) {
            $reward->update(['claimed' => true]);
            return ['unlocked' => true, 'message' => 'Hidden deal unlocked!'];
        }
        return null;
    }

    public static function hasRedeem5Reward(User $user): bool
    {
        return UserReward::where('user_id', $user->id)->where('type', 'redeem_5')->exists();
    }
}
