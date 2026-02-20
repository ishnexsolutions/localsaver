<?php

namespace App\Http\Controllers;

use App\Services\CouponRankingService;
use App\Services\TrendingService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        private CouponRankingService $rankingService,
        private TrendingService $trendingService
    ) {}

    public function index(Request $request)
    {
        $lat = $request->query('lat') ? (float) $request->query('lat') : (auth()->user()?->lat);
        $lng = $request->query('lng') ? (float) $request->query('lng') : (auth()->user()?->lng);
        $category = $request->query('category');

        if (auth()->check()) {
            auth()->user()->update(['last_active_at' => now()]);
            if (auth()->user()->role === 'user' && !auth()->user()->onboarding_completed) {
                return redirect()->route('onboarding.user');
            }
        }

        $coupons = $this->rankingService->getRankedFeed($lat, $lng, auth()->user(), $category);
        $trending = $this->trendingService->getTrendingNearLocation($lat, $lng, 5);

        return view('home', compact('coupons', 'trending', 'lat', 'lng', 'category'));
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        if (auth()->check()) {
            auth()->user()->update([
                'lat' => $request->lat,
                'lng' => $request->lng,
                'last_active_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
