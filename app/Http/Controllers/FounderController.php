<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsDaily;
use App\Models\Business;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Redemption;
use App\Models\User;
use App\Services\AnalyticsTrackingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FounderController extends Controller
{
    public function __construct(
        private AnalyticsTrackingService $analytics
    ) {}

    public function dashboard()
    {
        $today = today();
        $metrics = $this->analytics->getPlatformMetrics();

        $dau = User::where('role', 'user')->whereDate('last_active_at', $today)->count();
        $wau = User::where('role', 'user')->where('last_active_at', '>=', $today->copy()->subDays(7))->count();
        $newUsers = User::where('role', 'user')->whereDate('created_at', $today)->count();
        $redemptionsToday = Redemption::whereDate('created_at', $today)->count();
        $activeBusinesses = Business::where('activated', true)->where('verified', true)->count();
        $revenueToday = Payment::where('status', 'completed')->whereDate('created_at', $today)->sum('amount');
        $conversionRate = $metrics['conversion_rate'] ?? 0;
        $retentionD7 = $metrics['retention_d7'] ?? 0;

        $topCoupon = Coupon::where('status', 'active')
            ->orderByDesc('used_count')
            ->with('business')
            ->first();

        $dropOff = [
            'signed_up' => User::where('role', 'user')->count(),
            'with_location' => User::where('role', 'user')->whereNotNull('lat')->count(),
            'redeemed_once' => User::where('role', 'user')->where('redemption_count', '>=', 1)->count(),
            'redeemed_5plus' => User::where('role', 'user')->where('redemption_count', '>=', 5)->count(),
        ];

        $history = AnalyticsDaily::orderByDesc('date')->limit(14)->get();

        return view('founder.dashboard', compact(
            'dau', 'wau', 'newUsers', 'redemptionsToday', 'activeBusinesses',
            'revenueToday', 'conversionRate', 'retentionD7', 'topCoupon', 'dropOff', 'history'
        ));
    }
}
