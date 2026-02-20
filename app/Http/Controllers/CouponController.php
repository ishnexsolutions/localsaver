<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponComplaint;
use App\Services\BusinessAnalyticsService;
use App\Services\CouponRankingService;
use App\Services\CouponService;
use App\Services\FraudProtectionService;
use App\Services\ReferralService;
use App\Services\RetentionService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        private CouponService $couponService,
        private BusinessAnalyticsService $analyticsService,
        private FraudProtectionService $fraudService,
        private ReferralService $referralService,
        private RetentionService $retentionService,
        private CouponRankingService $rankingService
    ) {}

    public function show(Request $request, Coupon $coupon)
    {
        $coupon->load(['business', 'partnerBusiness']);
        $coupon->incrementViewCount();

        $ipHash = $request->ip() ? \App\Services\FraudProtectionService::hashIp($request->ip()) : null;
        $this->analyticsService->recordClick(
            $coupon,
            auth()->id(),
            $ipHash,
            $request->session()->getId()
        );

        $canRedeem = true;
        $redeemMessage = '';
        if (auth()->check()) {
            [$canRedeem, $redeemMessage] = $this->couponService->canRedeem($coupon, auth()->user());
        }

        $distance = null;
        if (auth()->check() && auth()->user()->lat && auth()->user()->lng) {
            $distance = round($this->couponService->haversineDistance(
                auth()->user()->lat,
                auth()->user()->lng,
                $coupon->business->lat,
                $coupon->business->lng
            ), 1);
        }

        return view('coupons.show', compact('coupon', 'canRedeem', 'redeemMessage', 'distance'));
    }

    public function redeem(Request $request, Coupon $coupon)
    {
        $this->authorize('redeem', $coupon);

        $ipHash = $request->ip() ? \App\Services\FraudProtectionService::hashIp($request->ip()) : null;
        $deviceHash = $request->userAgent() ? \App\Services\FraudProtectionService::hashDevice($request->userAgent()) : null;

        [$fraudOk, $fraudMessage] = $this->fraudService->canRedeem(auth()->user(), $coupon->id, $ipHash, $deviceHash);
        if (!$fraudOk) {
            return back()->with('error', $fraudMessage);
        }

        [$canRedeem, $message] = $this->couponService->canRedeem($coupon, auth()->user());
        if (!$canRedeem) {
            return back()->with('error', $message);
        }

        if ($coupon->redemptions()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You have already redeemed this coupon.');
        }

        $savings = $coupon->discount_type === 'fixed'
            ? $coupon->discount_value
            : 50;

        \DB::transaction(function () use ($coupon, $savings) {
            $coupon->redemptions()->create([
                'user_id' => auth()->id(),
                'savings_amount' => $savings,
            ]);
            $coupon->increment('used_count');
            auth()->user()->increment('redemption_count');
            auth()->user()->increment('total_saved', $savings);
            auth()->user()->update(['last_active_at' => now()]);
        });

        $this->analyticsService->markClickAsConverted($coupon->id, auth()->id());
        $this->referralService->onReferredUserRedemption(auth()->user());
        $this->retentionService->updateRedemptionStreak(auth()->user());
        $this->rankingService->invalidatePopularityCache($coupon->id);
        app(\App\Services\CouponQualityService::class)->updateQualityScore($coupon);
        \App\Services\GrowthHookService::onRedemption(auth()->user(), $coupon);

        $milestone = $this->retentionService->checkSavingsMilestones(auth()->user());
        $prefs = auth()->user()->notificationPreference;
        if ($milestone && auth()->user()->pushSubscriptions->isNotEmpty() && (!$prefs || $prefs->milestones)) {
            \App\Jobs\SendPushNotificationJob::dispatch(
                auth()->user(),
                '🎉 Milestone reached!',
                "You've saved ₹{$milestone['total']} total!"
            );
        }

        return redirect()->route('profile')->with('success', "Congratulations! You've saved ₹{$savings}");
    }

    public function complain(Request $request, Coupon $coupon)
    {
        $request->validate([
            'reason' => 'required|string|in: misleading,fake_expired,not_honored,other',
            'details' => 'nullable|string|max:1000',
        ]);

        if (CouponComplaint::where('coupon_id', $coupon->id)->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You have already reported this coupon.');
        }

        CouponComplaint::create([
            'coupon_id' => $coupon->id,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'details' => $request->details,
        ]);

        $coupon->increment('complaint_count');
        app(\App\Services\CouponQualityService::class)->updateQualityScore($coupon);

        return back()->with('success', 'Thank you for your report. We will review it.');
    }

    public function share(Coupon $coupon)
    {
        $result = \App\Services\GrowthHookService::onShare(auth()->user());
        return back()->with('success', $result ? $result['message'] : 'Link copied! Share to unlock rewards.');
    }
}
