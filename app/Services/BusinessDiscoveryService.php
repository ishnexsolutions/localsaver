<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BusinessDiscoveryService
{
    private const CACHE_TTL = 600;
    private const EARTH_RADIUS = 6371;

    public function getNearbyBusinesses(?float $lat, ?float $lng, ?string $category = null, int $limit = 20): Collection
    {
        if (!$lat || !$lng) {
            return $this->getAllBusinesses($category, $limit);
        }

        $cacheKey = "businesses_near:{$lat}:{$lng}:{$category}:{$limit}";
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($lat, $lng, $category, $limit) {
            $query = Business::query()
                ->where('activated', true)
                ->where('verified', true)
                ->select('businesses.*')
                ->selectRaw("(
                    " . self::EARTH_RADIUS . " * acos(
                        cos(radians(?)) * cos(radians(businesses.lat)) *
                        cos(radians(businesses.lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(businesses.lat))
                    )
                ) AS distance", [$lat, $lng, $lat])
                ->orderBy('distance')
                ->limit($limit);

            if ($category) {
                $query->where('category', $category);
            }

            return $query->withCount(['coupons' => fn ($q) => $q->where('status', 'active')])->get();
        });
    }

    public function getTrendingBusinesses(?float $lat, ?float $lng, int $limit = 10): Collection
    {
        $cacheKey = "businesses_trending:" . ($lat ?? 0) . ":" . ($lng ?? 0) . ":{$limit}";
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($lat, $lng, $limit) {
            $query = Business::where('activated', true)
                ->where('verified', true)
                ->withCount(['coupons' => fn ($q) => $q->where('status', 'active')])
                ->orderByDesc('popularity_score')
                ->limit($limit);

            if ($lat && $lng) {
                $query->selectRaw("businesses.*, (
                    " . self::EARTH_RADIUS . " * acos(
                        cos(radians(?)) * cos(radians(businesses.lat)) *
                        cos(radians(businesses.lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(businesses.lat))
                    )
                ) AS distance", [$lat, $lng, $lat])
                    ->orderBy('distance');
            }

            return $query->get();
        });
    }

    public function getBusinessesWithCustomerOverlap(Business $business, int $limit = 5): Collection
    {
        return BusinessNetworkAnalyticsService::getOverlapSuggestions($business, $limit);
    }

    private function getAllBusinesses(?string $category, int $limit): Collection
    {
        $query = Business::where('activated', true)
            ->where('verified', true)
            ->withCount(['coupons' => fn ($q) => $q->where('status', 'active')])
            ->orderByDesc('popularity_score')
            ->limit($limit);

        if ($category) {
            $query->where('category', $category);
        }

        return $query->get();
    }

    public function updatePopularityScore(Business $business): void
    {
        $score = $business->coupons()->sum('used_count')
            + ($business->coupons()->sum('view_count') / 10);
        $business->update(['popularity_score' => (int) min($score, 999999)]);
    }

    public function invalidateCache(): void
    {
        Cache::flush();
    }
}
