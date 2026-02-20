<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessRating;
use App\Services\BusinessDiscoveryService;
use App\Services\BusinessNetworkAnalyticsService;
use App\Services\BusinessTrustService;
use Illuminate\Http\Request;

class BusinessProfileController extends Controller
{
    public function __construct(
        private BusinessDiscoveryService $discoveryService
    ) {}

    public function show(Business $business)
    {
        $business->load(['coupons' => fn ($q) => $q->active()->with('business', 'partnerBusiness'), 'partners']);
        $business->loadCount('coupons');

        $totalRedemptions = $business->coupons->sum('used_count')
            + $business->couponsAsPartner()->sum('used_count');

        $customersAlsoVisit = BusinessNetworkAnalyticsService::getOverlapSuggestions($business, 5);

        $trustScore = app(BusinessTrustService::class)->getTrustScore($business);
        $avgRating = $business->ratings()->avg('rating');
        $ratingCount = $business->ratings()->count();
        $userRating = auth()->check() ? $business->ratings()->where('user_id', auth()->id())->first() : null;

        return view('businesses.show', compact('business', 'totalRedemptions', 'customersAlsoVisit', 'trustScore', 'avgRating', 'ratingCount', 'userRating'));
    }

    public function rate(Request $request, Business $business)
    {
        $request->validate(['rating' => 'required|integer|min:1|max:5', 'comment' => 'nullable|string|max:500']);

        BusinessRating::updateOrCreate(
            ['business_id' => $business->id, 'user_id' => auth()->id()],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        app(BusinessTrustService::class)->recalculate($business);

        return back()->with('success', 'Thanks for your rating!');
    }
}
