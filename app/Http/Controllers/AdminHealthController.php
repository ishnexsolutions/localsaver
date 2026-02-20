<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;

class AdminHealthController extends Controller
{
    public function index()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
            'empty_categories' => $this->getEmptyCategories(),
            'weak_coupons' => $this->getWeakCoupons(),
            'inactive_businesses' => $this->getInactiveBusinesses(),
            'fraud_alerts' => $this->getFraudAlerts(),
        ];

        return view('admin.health', compact('checks'));
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            DB::connection()->getDatabaseName();
            return ['status' => 'ok', 'message' => 'Connected'];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkCache(): array
    {
        try {
            Cache::put('health_check', 1, 10);
            $v = Cache::get('health_check');
            return ['status' => $v ? 'ok' : 'warning', 'message' => $v ? 'Working' : 'Read failed'];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkQueue(): array
    {
        try {
            return ['status' => 'ok', 'message' => config('queue.default')];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function getEmptyCategories(): array
    {
        $categories = ['food', 'salon', 'health', 'services', 'shopping'];
        $empty = [];
        foreach ($categories as $cat) {
            $count = \App\Models\Business::where('category', $cat)->where('activated', true)->where('verified', true)->count();
            if ($count === 0) {
                $empty[] = $cat;
            }
        }
        return ['count' => count($empty), 'categories' => $empty];
    }

    private function getWeakCoupons(): int
    {
        return \App\Models\Coupon::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('quality_score')->orWhere('quality_score', '<', 40);
            })
            ->count();
    }

    private function getInactiveBusinesses(): int
    {
        return \App\Models\Business::where('activated', true)
            ->whereDoesntHave('coupons', fn ($q) => $q->where('status', 'active'))
            ->count();
    }

    private function getFraudAlerts(): int
    {
        return \App\Models\FraudFlag::where('status', 'pending')->count();
    }
}
