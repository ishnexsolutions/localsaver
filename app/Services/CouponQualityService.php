<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class CouponQualityService
{
    public function calculateQualityScore(Coupon $coupon): float
    {
        $views = max(1, $coupon->view_count);
        $redemptionRate = min(1, $coupon->used_count / $views);
        $discountStrength = $this->getDiscountStrength($coupon);
        $expiryDiscipline = $this->getExpiryDiscipline($coupon);
        $avgRating = $coupon->rating_count > 0 ? ($coupon->rating_sum / $coupon->rating_count) / 5 : 1;
        $complaintPenalty = max(0, 1 - ($coupon->complaint_count * 0.1));

        $score = (
            $redemptionRate * 30 +
            $discountStrength * 25 +
            $expiryDiscipline * 20 +
            $avgRating * 15 +
            $complaintPenalty * 10
        );

        return round(min(100, max(0, $score)), 2);
    }

    public function updateQualityScore(Coupon $coupon): void
    {
        $score = $this->calculateQualityScore($coupon);
        $coupon->update(['quality_score' => $score]);
    }

    public function getFeedPenalty(Coupon $coupon): float
    {
        $score = $coupon->quality_score ?? $this->calculateQualityScore($coupon);
        if ($score >= 70) return 1;
        if ($score >= 50) return 0.8;
        if ($score >= 30) return 0.5;
        return 0.2;
    }

    private function getDiscountStrength(Coupon $coupon): float
    {
        if ($coupon->discount_type === 'percentage') {
            return min(1, $coupon->discount_value / 50);
        }
        return min(1, $coupon->discount_value / 200);
    }

    private function getExpiryDiscipline(Coupon $coupon): float
    {
        $daysLeft = now()->diffInDays($coupon->expiry_date, false);
        if ($daysLeft <= 0) return 0;
        if ($daysLeft >= 30) return 1;
        return $daysLeft / 30;
    }
}
