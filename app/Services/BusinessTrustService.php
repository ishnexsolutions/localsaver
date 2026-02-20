<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessTrustScore;
use Illuminate\Support\Facades\DB;

class BusinessTrustService
{
    public function calculateTrustScore(Business $business): float
    {
        $avgRating = $business->ratings()->avg('rating') ?? 0;
        $ratingScore = ($avgRating / 5) * 40;
        $verifiedBonus = $business->verified ? 20 : 0;
        $authenticityScore = 40;
        $fraudFlags = 0;
        $fraudPenalty = 0;

        $score = min(100, max(0, $ratingScore + $authenticityScore + $verifiedBonus - $fraudPenalty));

        BusinessTrustScore::updateOrCreate(
            ['business_id' => $business->id],
            [
                'score' => round($score, 2),
                'factors' => [
                    'avg_rating' => $avgRating,
                    'fraud_flags' => $fraudFlags,
                    'verified' => $business->verified,
                ],
            ]
        );

        return round($score, 2);
    }

    public function recalculate(Business $business): float
    {
        return $this->calculateTrustScore($business);
    }

    public function getTrustScore(Business $business): ?float
    {
        $trust = BusinessTrustScore::where('business_id', $business->id)->first();
        return $trust ? (float) $trust->score : null;
    }
}
