<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessAnalyticsService
{
    public function getDashboardMetrics(Business $business): array
    {
        $couponIds = $business->coupons->pluck('id');

        $clicks = \App\Models\CouponClick::whereIn('coupon_id', $couponIds)->count();
        $uniqueUsers = \App\Models\CouponClick::whereIn('coupon_id', $couponIds)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
        $redemptions = $business->coupons->sum('used_count');
        $views = $business->coupons->sum('view_count');

        $conversionRate = $clicks > 0 ? round(($redemptions / $clicks) * 100, 1) : 0;

        $firstTimeCount = $this->getFirstTimeRedemptions($business);
        $repeatCount = $redemptions - $firstTimeCount;

        $peakHour = $this->getPeakRedemptionHour($business);

        $suggestion = null;
        $inactiveUserCount = $this->getInactiveUsersNearby($business);
        if ($inactiveUserCount > 10) {
            $suggestion = "Create a comeback coupon for {$inactiveUserCount}+ inactive users in your area.";
        }

        return [
            'total_views' => $views,
            'total_clicks' => $clicks,
            'total_redemptions' => $redemptions,
            'unique_users' => $uniqueUsers,
            'first_time_customers' => $firstTimeCount,
            'repeat_customers' => $repeatCount,
            'conversion_rate' => $conversionRate,
            'peak_redemption_hour' => $peakHour,
            'suggestion' => $suggestion,
        ];
    }

    private function getFirstTimeRedemptions(Business $business): int
    {
        return DB::table('redemptions')
            ->join('coupons', 'redemptions.coupon_id', '=', 'coupons.id')
            ->where('coupons.business_id', $business->id)
            ->selectRaw('user_id, COUNT(*) as cnt')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) = 1')
            ->count();
    }

    private function getPeakRedemptionHour(Business $business): ?int
    {
        $hour = DB::table('redemptions')
            ->join('coupons', 'redemptions.coupon_id', '=', 'coupons.id')
            ->where('coupons.business_id', $business->id)
            ->selectRaw('HOUR(redemptions.created_at) as hour, COUNT(*) as cnt')
            ->groupBy('hour')
            ->orderByDesc('cnt')
            ->value('hour');
        return $hour;
    }

    private function getInactiveUsersNearby(Business $business): int
    {
        $cutoff = now()->subDays(14);
        return \App\Models\User::where('role', 'user')
            ->where(function ($q) use ($cutoff) {
                $q->where('last_active_at', '<', $cutoff)->orWhereNull('last_active_at');
            })
            ->whereNotNull('lat')
            ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) <= ?", [
                $business->lat, $business->lng, $business->lat, $business->radius_km
            ])
            ->count();
    }

    public function recordClick(Coupon $coupon, ?int $userId, ?string $ipHash, ?string $sessionId): void
    {
        $isUnique = true;
        if ($userId) {
            $exists = \App\Models\CouponClick::where('coupon_id', $coupon->id)
                ->where('user_id', $userId)
                ->exists();
            $isUnique = !$exists;
        }

        \App\Models\CouponClick::create([
            'coupon_id' => $coupon->id,
            'user_id' => $userId,
            'ip_hash' => $ipHash,
            'session_id' => $sessionId,
            'is_unique_user' => $isUnique,
        ]);
    }

    public function markClickAsConverted(int $couponId, int $userId): void
    {
        \App\Models\CouponClick::where('coupon_id', $couponId)
            ->where('user_id', $userId)
            ->update(['converted_to_redemption' => true]);
    }
}
