<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Redemption;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TrendingService
{
    private const CACHE_TTL = 900;
    private const TRENDING_HOURS = 24;

    public function getTrendingNearLocation(?float $lat, ?float $lng, int $limit = 5): Collection
    {
        $cacheKey = "trending:" . ($lat ?? 0) . ":" . ($lng ?? 0) . ":{$limit}";
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($lat, $lng, $limit) {
            $since = now()->subHours(self::TRENDING_HOURS);

            $couponIds = DB::table('redemptions')
                ->join('coupons', 'redemptions.coupon_id', '=', 'coupons.id')
                ->where('redemptions.created_at', '>=', $since)
                ->when($lat && $lng, function ($q) use ($lat, $lng) {
                    $q->join('businesses', 'coupons.business_id', '=', 'businesses.id')
                        ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(businesses.lat)) * cos(radians(businesses.lng) - radians(?)) + sin(radians(?)) * sin(radians(businesses.lat)))) <= 25", [$lat, $lng, $lat]);
                })
                ->select('coupons.id')
                ->groupBy('coupons.id')
                ->orderByDesc(DB::raw('COUNT(redemptions.id)'))
                ->limit($limit)
                ->pluck('id');

            if ($couponIds->isEmpty()) {
                return collect();
            }

            return Coupon::whereIn('id', $couponIds)->active()->with('business')->get();
        });
    }

    public function getRedemptionDensityByArea(float $lat, float $lng, float $radiusKm = 50): array
    {
        $cacheKey = "heatmap:{$lat}:{$lng}:{$radiusKm}";
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($lat, $lng, $radiusKm) {
            return Redemption::join('coupons', 'redemptions.coupon_id', '=', 'coupons.id')
                ->join('businesses', 'coupons.business_id', '=', 'businesses.id')
                ->where('redemptions.created_at', '>=', now()->subDays(7))
                ->selectRaw('
                    businesses.lat,
                    businesses.lng,
                    businesses.name,
                    COUNT(redemptions.id) as count,
                    (6371 * acos(cos(radians(?)) * cos(radians(businesses.lat)) *
                    cos(radians(businesses.lng) - radians(?)) + sin(radians(?)) * sin(radians(businesses.lat)))) AS distance
                ', [$lat, $lng, $lat])
                ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(businesses.lat)) *
                    cos(radians(businesses.lng) - radians(?)) + sin(radians(?)) * sin(radians(businesses.lat)))) <= ?",
                    [$lat, $lng, $lat, $radiusKm])
                ->groupBy('businesses.id', 'businesses.lat', 'businesses.lng', 'businesses.name')
                ->get()
                ->toArray();
        });
    }

    public function invalidateTrendingCache(): void
    {
        Cache::flush();
    }
}
