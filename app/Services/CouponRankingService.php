<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CouponRankingService
{
    public function __construct(
        private CouponQualityService $qualityService
    ) {}

    private const BOOST_WEIGHT = 100;
    private const DISTANCE_WEIGHT = 30;
    private const EXPIRY_URGENCY_WEIGHT = 25;
    private const POPULARITY_WEIGHT = 20;
    private const PREFERENCE_WEIGHT = 25;
    private const CACHE_TTL = 300;
    private const POPULARITY_CACHE_KEY = 'coupon_popularity';

    public function getRankedFeed(?float $lat, ?float $lng, ?User $user, ?string $category, int $limit = 20): Collection
    {
        $cacheKey = $this->getCacheKey($lat, $lng, $user, $category, $limit);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($lat, $lng, $user, $category, $limit) {
            $coupons = $this->getBaseCoupons($lat, $lng, $category, $limit + 20);
            $userPreferences = $user ? $this->getUserCategoryPreferences($user) : [];
            return $this->scoreAndSort($coupons, $lat, $lng, $userPreferences)->take($limit)->values();
        });
    }

    private function getBaseCoupons(?float $lat, ?float $lng, ?string $category, int $limit): Collection
    {
        $earthRadius = 6371;

        $query = Coupon::active()
            ->select('coupons.*')
            ->join('businesses', 'coupons.business_id', '=', 'businesses.id')
            ->leftJoin('businesses as partner_biz', 'coupons.partner_business_id', '=', 'partner_biz.id')
            ->where('businesses.activated', true)
            ->where('businesses.verified', true);

        if ($category) {
            $query->where('businesses.category', $category);
        }

        if ($lat && $lng) {
            $query->selectRaw("(
                {$earthRadius} * acos(
                    cos(radians(?)) * cos(radians(businesses.lat)) *
                    cos(radians(businesses.lng) - radians(?)) +
                    sin(radians(?)) * sin(radians(businesses.lat))
                ) AS distance", [$lat, $lng, $lat])
                ->whereRaw("(
                    {$earthRadius} * acos(
                        cos(radians(?)) * cos(radians(businesses.lat)) *
                        cos(radians(businesses.lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(businesses.lat))
                    ) <= COALESCE(coupons.radius_km, businesses.radius_km, 5)", [$lat, $lng, $lat])
                ->whereRaw("(coupons.partner_business_id IS NULL OR (partner_biz.id IS NOT NULL AND (
                    {$earthRadius} * acos(
                        cos(radians(?)) * cos(radians(partner_biz.lat)) *
                        cos(radians(partner_biz.lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(partner_biz.lat))
                    ) <= COALESCE(partner_biz.radius_km, 5)
                )))", [$lat, $lng, $lat]);
        } else {
            $query->selectRaw("999 AS distance");
        }

        return $query->with(['business', 'partnerBusiness'])->limit($limit)->get();
    }

    private function scoreAndSort(Collection $coupons, ?float $lat, ?float $lng, array $userPreferences): Collection
    {
        $popularityScores = $this->getPopularityScores($coupons->pluck('id'));

        return $coupons->map(function ($coupon) use ($lat, $lng, $userPreferences, $popularityScores) {
            $score = 0;

            $score += $this->getBoostScore($coupon);
            $score += $this->getDistanceScore($coupon, $lat, $lng);
            $score += $this->getExpiryUrgencyScore($coupon);
            $score += ($popularityScores[$coupon->id] ?? 0) * self::POPULARITY_WEIGHT / 100;
            $score += $this->getPreferenceScore($coupon, $userPreferences);

            $qualityPenalty = $this->qualityService->getFeedPenalty($coupon);
            $score *= $qualityPenalty;

            $coupon->rank_score = round($score, 2);
            return $coupon;
        })->sortByDesc('rank_score')->values();
    }

    private function getBoostScore($coupon): float
    {
        if (!$coupon->is_boosted) {
            return 0;
        }
        $base = self::BOOST_WEIGHT;
        if ($coupon->featured_flag ?? false) {
            $base += 20;
        }
        return $base + ($coupon->priority_score ?? 0);
    }

    private function getDistanceScore($coupon, ?float $lat, ?float $lng): float
    {
        if (!$lat || !$lng || !isset($coupon->distance)) {
            return self::DISTANCE_WEIGHT;
        }
        $distance = (float) $coupon->distance;
        if ($distance <= 1) return self::DISTANCE_WEIGHT;
        if ($distance <= 3) return self::DISTANCE_WEIGHT * 0.8;
        if ($distance <= 5) return self::DISTANCE_WEIGHT * 0.5;
        return max(0, self::DISTANCE_WEIGHT * (1 - $distance / 10));
    }

    private function getExpiryUrgencyScore($coupon): float
    {
        $daysLeft = now()->diffInDays($coupon->expiry_date, false);
        if ($daysLeft <= 0) return 0;
        if ($daysLeft <= 1) return self::EXPIRY_URGENCY_WEIGHT;
        if ($daysLeft <= 3) return self::EXPIRY_URGENCY_WEIGHT * 0.8;
        if ($daysLeft <= 7) return self::EXPIRY_URGENCY_WEIGHT * 0.5;
        return self::EXPIRY_URGENCY_WEIGHT * 0.2;
    }

    private function getPreferenceScore($coupon, array $userPreferences): float
    {
        if (empty($userPreferences)) {
            return 0;
        }
        $category = $coupon->business->category ?? null;
        $pref = $userPreferences[$category] ?? 0;
        return $pref * self::PREFERENCE_WEIGHT;
    }

    private function getPopularityScores($couponIds): array
    {
        $scores = [];
        foreach ($couponIds as $id) {
            $scores[$id] = $this->getCachedPopularity($id);
        }
        $max = max(array_values($scores)) ?: 1;
        foreach ($scores as $id => $val) {
            $scores[$id] = $val / $max;
        }
        return $scores;
    }

    public function getCachedPopularity(int $couponId): float
    {
        $key = self::POPULARITY_CACHE_KEY . ":{$couponId}";
        return (float) Cache::remember($key, 3600, fn () => \App\Models\Redemption::where('coupon_id', $couponId)->count());
    }

    public function invalidatePopularityCache(int $couponId): void
    {
        Cache::forget(self::POPULARITY_CACHE_KEY . ":{$couponId}");
    }

    public function invalidateFeedCacheForCoupon(int $couponId): void
    {
        $this->invalidatePopularityCache($couponId);
    }

    private function getUserCategoryPreferences(User $user): array
    {
        $redemptions = $user->redemptions()
            ->with('coupon.business')
            ->get();
        $counts = [];
        foreach ($redemptions as $r) {
            $cat = $r->coupon->business->category ?? 'other';
            $counts[$cat] = ($counts[$cat] ?? 0) + 1;
        }
        $total = array_sum($counts) ?: 1;
        foreach ($counts as $cat => $c) {
            $counts[$cat] = $c / $total;
        }
        return $counts;
    }

    private function getCacheKey(?float $lat, ?float $lng, ?User $user, ?string $category, int $limit): string
    {
        $lat = round($lat ?? 0, 3);
        $lng = round($lng ?? 0, 3);
        $userId = $user?->id ?? 'guest';
        return "feed_ranked:{$lat}:{$lng}:{$userId}:{$category}:{$limit}";
    }

}
