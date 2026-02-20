<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Services\BusinessDiscoveryService;
use App\Services\CouponRankingService;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __construct(
        private CouponRankingService $rankingService,
        private BusinessDiscoveryService $discoveryService
    ) {}

    public function userSteps(Request $request)
    {
        $user = auth()->user();
        if ($user->onboarding_completed) {
            return redirect()->route('home');
        }
        $step = (int) ($request->query('step') ?? 1);
        return view('onboarding.user', compact('step', 'user'));
    }

    public function completeUserStep(Request $request)
    {
        $request->validate(['step' => 'required|integer|min:1|max:5']);
        $user = auth()->user();

        switch ($request->step) {
            case 1:
                $request->validate(['lat' => 'required|numeric', 'lng' => 'required|numeric']);
                $user->update([
                    'lat' => $request->lat,
                    'lng' => $request->lng,
                ]);
                return redirect()->route('onboarding.user', ['step' => 2]);
            case 2:
                $request->validate(['categories' => 'required|array', 'categories.*' => 'in:food,salon,health,services,shopping']);
                $user->update(['preferred_categories' => $request->categories]);
                return redirect()->route('onboarding.user', ['step' => 3]);
            case 3:
                $lat = $user->lat;
                $lng = $user->lng;
                $cats = $user->preferred_categories ?? ['food', 'salon', 'health', 'services', 'shopping'];
                $potentialSavings = $this->rankingService->getRankedFeed($lat, $lng, $user, $cats[0] ?? null, 5)
                    ->sum(fn ($c) => $c->discount_type === 'fixed' ? $c->discount_value : 50);
                return view('onboarding.user', ['step' => 3, 'user' => $user, 'potentialSavings' => $potentialSavings]);
            case 4:
                return view('onboarding.user', ['step' => 4, 'user' => $user]);
            case 5:
                $user->update(['onboarding_completed' => true]);
                \App\Models\NotificationPreference::firstOrCreate(
                    ['user_id' => $user->id],
                    ['daily_deals' => true, 'flash_deals' => true, 'milestones' => true, 'comeback' => true]
                );
                return redirect()->route('home')->with('success', 'Welcome! Your personalized feed is ready.');
        }

        return redirect()->route('onboarding.user');
    }

    public function businessSteps(Request $request)
    {
        $business = auth()->user()->business;
        if (!$business || !$business->activated || $business->onboarding_completed) {
            return redirect()->route('business.dashboard');
        }
        $step = (int) ($request->query('step') ?? 1);
        return view('onboarding.business', compact('step', 'business'));
    }

    public function completeBusinessStep(Request $request)
    {
        $request->validate(['step' => 'required|integer|min:1|max:4']);
        $business = auth()->user()->business;

        switch ($request->step) {
            case 1:
                $business->update(['onboarding_completed' => true]);
                return redirect()->route('business.dashboard')->with('success', 'Welcome to your dashboard!');
            case 2:
                return redirect()->route('business.coupons.create')->with('onboarding', true);
            case 3:
                $couponsCount = $business->coupons()->count();
                $estimatedReach = $couponsCount * 50;
                return view('onboarding.business', ['step' => 3, 'business' => $business, 'estimatedReach' => $estimatedReach]);
            case 4:
                $business->update(['onboarding_completed' => true]);
                return redirect()->route('business.dashboard')->with('success', 'Your business is ready!');
        }

        return redirect()->route('business.dashboard');
    }
}
