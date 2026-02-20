<?php

namespace App\Services;

use App\Models\Coupon;
use Carbon\Carbon;

class BoostService
{
    public function boostCoupon(Coupon $coupon, int $days = 7, bool $featured = false, int $priorityScore = 0): void
    {
        $coupon->update([
            'is_boosted' => true,
            'boosted_until' => now()->addDays($days),
            'featured_flag' => $featured,
            'priority_score' => $priorityScore,
        ]);

        app(CouponRankingService::class)->invalidateFeedCacheForCoupon($coupon->id);
    }

    public function expireBoostedCoupons(): int
    {
        return Coupon::where('is_boosted', true)
            ->whereNotNull('boosted_until')
            ->where('boosted_until', '<', now())
            ->update([
                'is_boosted' => false,
                'featured_flag' => false,
                'priority_score' => 0,
                'boosted_until' => null,
            ]);
    }

    public function getFeaturedCoupons(?float $lat, ?float $lng, int $limit = 3): \Illuminate\Support\Collection
    {
        $query = Coupon::active()
            ->where('featured_flag', true)
            ->with('business');

        if ($lat && $lng) {
            $query->join('businesses', 'coupons.business_id', '=', 'businesses.id')
                ->select('coupons.*')
                ->selectRaw("(6371 * acos(cos(radians(?)) * cos(radians(businesses.lat)) * cos(radians(businesses.lng) - radians(?)) + sin(radians(?)) * sin(radians(businesses.lat)))) AS distance", [$lat, $lng, $lat])
                ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(businesses.lat)) * cos(radians(businesses.lng) - radians(?)) + sin(radians(?)) * sin(radians(businesses.lat)))) <= 50", [$lat, $lng, $lat])
                ->orderBy('distance');
        }

        return $query->limit($limit)->get();
    }
}
