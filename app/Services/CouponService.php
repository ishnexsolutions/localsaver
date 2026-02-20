<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CouponService
{
    public function getNearbyCoupons(?float $lat, ?float $lng, ?string $category = null, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        if (!$lat || !$lng) {
            return Coupon::active()->limit($limit)->get();
        }

        $cacheKey = "coupons_near_{$lat}_{$lng}_{$category}_{$limit}";
        return Cache::remember($cacheKey, 300, function () use ($lat, $lng, $category, $limit) {
            $earthRadius = 6371; // km

            $query = Coupon::active()
                ->select('coupons.*')
                ->selectRaw("(
                    {$earthRadius} * acos(
                        cos(radians(?)) * cos(radians(businesses.lat)) *
                        cos(radians(businesses.lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(businesses.lat))
                    ) AS distance", [$lat, $lng, $lat])
                ->join('businesses', 'coupons.business_id', '=', 'businesses.id')
                ->where('businesses.activated', true)
                ->where('businesses.verified', true);

            if ($category) {
                $query->where('businesses.category', $category);
            }
            $query->whereRaw("(
                {$earthRadius} * acos(
                    cos(radians(?)) * cos(radians(businesses.lat)) *
                    cos(radians(businesses.lng) - radians(?)) +
                    sin(radians(?)) * sin(radians(businesses.lat))
                ) <= COALESCE(coupons.radius_km, businesses.radius_km, 5)", [$lat, $lng, $lat]);

            return $query
                ->orderByRaw('coupons.is_boosted DESC, distance ASC, coupons.expiry_date ASC')
                ->limit($limit)
                ->get();
        });
    }

    public function canRedeem(Coupon $coupon, User $user): array
    {
        if ($coupon->isExpired()) {
            return [false, 'This coupon has expired.'];
        }

        if ($coupon->used_count >= $coupon->max_redemptions) {
            return [false, 'This coupon has reached its redemption limit.'];
        }

        if (!$coupon->isWithinTime()) {
            return [false, 'This coupon is not valid at the current time.'];
        }

        if ($coupon->first_time_only && $user->redemptions()->where('coupon_id', $coupon->id)->exists()) {
            return [false, 'This coupon is for first-time customers only.'];
        }

        if ($user->redemptions()->where('coupon_id', $coupon->id)->exists()) {
            return [false, 'You have already redeemed this coupon.'];
        }

        if ($user->lat && $user->lng) {
            $distance = $this->haversineDistance(
                $user->lat,
                $user->lng,
                $coupon->business->lat,
                $coupon->business->lng
            );
            $radius = $coupon->radius_km ?? $coupon->business->radius_km ?? 5;
            if ($distance > $radius) {
                return [false, 'You must be within the coupon area to redeem.'];
            }
            if ($coupon->isJointCoupon() && $coupon->partnerBusiness) {
                $distToPartner = $this->haversineDistance(
                    $user->lat,
                    $user->lng,
                    $coupon->partnerBusiness->lat,
                    $coupon->partnerBusiness->lng
                );
                $partnerRadius = $coupon->partnerBusiness->radius_km ?? 5;
                if ($distToPartner > $partnerRadius) {
                    return [false, 'You must be within both partner locations to redeem this joint coupon.'];
                }
            }
        }

        return [true, ''];
    }

    public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
