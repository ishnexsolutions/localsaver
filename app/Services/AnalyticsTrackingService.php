<?php

namespace App\Services;

use App\Models\AnalyticsDaily;
use App\Models\Business;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Redemption;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsTrackingService
{
    public function recordDailySnapshot(): void
    {
        $date = today();

        $dau = User::where('role', 'user')
            ->whereDate('last_active_at', $date)
            ->count();

        $wau = User::where('role', 'user')
            ->where('last_active_at', '>=', $date->copy()->subDays(7))
            ->count();

        $redemptions = Redemption::whereDate('created_at', $date)->count();
        $views = DB::table('coupon_clicks')->whereDate('created_at', $date)->count();
        $conversionRate = $views > 0 ? round(($redemptions / $views) * 100, 2) : 0;
        $revenue = Payment::where('status', 'completed')->whereDate('created_at', $date)->sum('amount');

        AnalyticsDaily::updateOrCreate(
            ['date' => $date],
            [
                'dau' => $dau,
                'wau' => $wau,
                'total_users' => User::where('role', 'user')->count(),
                'total_businesses' => Business::count(),
                'total_coupons' => Coupon::active()->count(),
                'redemptions_count' => Redemption::count(),
                'revenue' => Payment::where('status', 'completed')->sum('amount'),
                'conversion_rate' => $conversionRate,
                'metadata' => [
                    'daily_redemptions' => $redemptions,
                    'daily_revenue' => $revenue,
                ],
            ]
        );
    }

    public function getPlatformMetrics(): array
    {
        $today = AnalyticsDaily::where('date', today())->first();
        $yesterday = AnalyticsDaily::where('date', today()->subDay())->first();

        return [
            'dau' => $today?->dau ?? 0,
            'wau' => $today?->wau ?? 0,
            'total_redemptions' => Redemption::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'conversion_rate' => $today?->conversion_rate ?? 0,
            'retention_d1' => $this->getRetentionRate(1),
            'retention_d7' => $this->getRetentionRate(7),
            'retention_d30' => $this->getRetentionRate(30),
            'daily_revenue' => $today?->metadata['daily_revenue'] ?? 0,
        ];
    }

    private function getRetentionRate(int $day): float
    {
        $cohortDate = today()->subDays($day);
        $signups = User::where('role', 'user')->whereDate('created_at', $cohortDate)->count();
        if ($signups === 0) {
            return 0;
        }
        $returned = User::where('role', 'user')
            ->whereDate('created_at', $cohortDate)
            ->where('last_active_at', '>=', $cohortDate->copy()->addDay())
            ->count();
        return round(($returned / $signups) * 100, 1);
    }
}
