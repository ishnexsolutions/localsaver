<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Redemption;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BusinessNetworkAnalyticsService
{
    public static function getOverlapSuggestions(Business $business, int $limit = 5): Collection
    {
        $userIds = Redemption::join('coupons', 'redemptions.coupon_id', '=', 'coupons.id')
            ->where('coupons.business_id', $business->id)
            ->pluck('redemptions.user_id');

        if ($userIds->isEmpty()) {
            return collect();
        }

        $overlaps = Redemption::join('coupons', 'redemptions.coupon_id', '=', 'coupons.id')
            ->join('businesses', 'coupons.business_id', '=', 'businesses.id')
            ->whereIn('redemptions.user_id', $userIds)
            ->where('businesses.id', '!=', $business->id)
            ->where('businesses.activated', true)
            ->where('businesses.verified', true)
            ->select('businesses.id', DB::raw('COUNT(DISTINCT redemptions.user_id) as overlap_count'))
            ->groupBy('businesses.id')
            ->orderByDesc('overlap_count')
            ->limit($limit)
            ->get();

        $businessIds = $overlaps->pluck('id');
        if ($businessIds->isEmpty()) {
            return collect();
        }

        $businesses = Business::whereIn('id', $businessIds)
            ->get()
            ->keyBy('id');

        return $overlaps->map(fn ($o) => (object) [
            'business' => $businesses->get($o->id),
            'overlap_count' => $o->overlap_count,
        ])->filter(fn ($o) => $o->business)->values();
    }

    public function getCustomersAlsoVisit(Business $business, int $limit = 5): Collection
    {
        return self::getOverlapSuggestions($business, $limit);
    }

    public function getPartnershipOpportunities(Business $business): Collection
    {
        $overlaps = self::getOverlapSuggestions($business, 10);
        $partnerIds = $business->partners()->pluck('businesses.id');
        $pendingIds = $business->partnershipRequestsSent()
            ->where('status', 'pending')
            ->pluck('target_business_id');

        return $overlaps->filter(function ($item) use ($partnerIds, $pendingIds) {
            return !$partnerIds->contains($item->business->id)
                && !$pendingIds->contains($item->business->id);
        })->take(5)->values();
    }
}
