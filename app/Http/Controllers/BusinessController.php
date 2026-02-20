<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Services\BusinessAnalyticsService;
use App\Services\CouponService;
use App\Services\PartnershipService;
use App\Services\RazorpayService;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function __construct(
        private CouponService $couponService,
        private RazorpayService $razorpayService,
        private BusinessAnalyticsService $analyticsService,
        private PartnershipService $partnershipService
    ) {}

    public function dashboard()
    {
        $business = auth()->user()->business;

        if (!$business->activated) {
            return redirect()->route('business.payment');
        }
        if (!$business->onboarding_completed) {
            return redirect()->route('onboarding.business');
        }

        $stats = $this->analyticsService->getDashboardMetrics($business);
        $coupons = $business->coupons()->with('partnerBusiness')->latest()->get();
        $jointCoupons = $business->couponsAsPartner()->with('business')
            ->where('status', 'active')
            ->where('expiry_date', '>=', now()->toDateString())
            ->get();
        $partnersCount = $business->partners()->count();
        $partnershipOpportunities = \App\Services\BusinessNetworkAnalyticsService::getOverlapSuggestions($business, 3);

        return view('business.dashboard', compact('stats', 'coupons', 'jointCoupons', 'partnersCount', 'partnershipOpportunities'));
    }

    public function paymentForm()
    {
        $business = auth()->user()->business;
        if ($business->activated) {
            return redirect()->route('business.dashboard');
        }

        $order = $this->razorpayService->createOrder($business, 'signup', 19900);
        return view('business.payment', $order);
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);

        $payment = \App\Models\Payment::where('razorpay_order_id', $request->razorpay_order_id)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($this->razorpayService->completePayment(
            $payment,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        )) {
            return redirect()->route('business.dashboard')->with('success', 'Business activated successfully!');
        }

        return back()->with('error', 'Payment verification failed.');
    }

    public function createCouponForm()
    {
        $partners = auth()->user()->business->partners;
        return view('business.coupons.create', compact('partners'));
    }

    public function storeCoupon(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'discount_value' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'max_redemptions' => 'required|integer|min:1',
            'expiry_date' => 'required|date|after:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'radius_km' => 'nullable|numeric|min:0.5|max:50',
            'first_time_only' => 'boolean',
            'partner_business_id' => 'nullable|exists:businesses,id',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'discount_value' => $request->discount_value,
            'discount_type' => $request->discount_type,
            'max_redemptions' => $request->max_redemptions,
            'expiry_date' => $request->expiry_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'radius_km' => $request->radius_km ?? 5,
            'first_time_only' => $request->boolean('first_time_only'),
        ];
        if ($request->partner_business_id && $this->partnershipService->canCreateJointCoupon(auth()->user()->business, Business::find($request->partner_business_id))) {
            $data['partner_business_id'] = $request->partner_business_id;
        }

        $coupon = auth()->user()->business->coupons()->create($data);
        app(\App\Services\CouponRankingService::class)->invalidateFeedCacheForCoupon($coupon->id);

        if (session('onboarding')) {
            return redirect()->route('onboarding.business', ['step' => 3]);
        }
        return redirect()->route('business.dashboard')->with('success', 'Coupon created successfully!');
    }

    public function editCoupon(Coupon $coupon)
    {
        $this->authorize('update', $coupon);
        return view('business.coupons.edit', compact('coupon'));
    }

    public function updateCoupon(Request $request, Coupon $coupon)
    {
        $this->authorize('update', $coupon);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'discount_value' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'max_redemptions' => 'required|integer|min:' . $coupon->used_count,
            'expiry_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'radius_km' => 'nullable|numeric|min:0.5|max:50',
            'first_time_only' => 'boolean',
        ]);

        $coupon->update($request->only([
            'title', 'description', 'discount_value', 'discount_type',
            'max_redemptions', 'expiry_date', 'start_time', 'end_time',
            'radius_km', 'first_time_only'
        ]));
        app(\App\Services\CouponRankingService::class)->invalidateFeedCacheForCoupon($coupon->id);
        app(\App\Services\CouponQualityService::class)->updateQualityScore($coupon);

        return redirect()->route('business.dashboard')->with('success', 'Coupon updated!');
    }

    public function destroyCoupon(Coupon $coupon)
    {
        $this->authorize('delete', $coupon);
        $id = $coupon->id;
        $coupon->delete();
        app(\App\Services\CouponRankingService::class)->invalidateFeedCacheForCoupon($id);
        return redirect()->route('business.dashboard')->with('success', 'Coupon deleted.');
    }