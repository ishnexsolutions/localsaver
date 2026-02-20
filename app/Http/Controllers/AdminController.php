<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessPartner;
use App\Models\Coupon;
use App\Models\FraudFlag;
use App\Models\Payment;
use App\Models\User;
use App\Services\AnalyticsTrackingService;
use App\Services\PartnershipService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private AnalyticsTrackingService $analytics,
        private PartnershipService $partnershipService
    ) {}

    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_businesses' => Business::count(),
            'total_coupons' => Coupon::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
        ];

        $metrics = $this->analytics->getPlatformMetrics();
        $pendingBusinesses = Business::where('verified', false)->with('user')->latest()->limit(10)->get();
        $pendingFraudCount = FraudFlag::where('status', 'pending')->count();

        return view('admin.dashboard', compact('stats', 'metrics', 'pendingBusinesses', 'pendingFraudCount'));
    }

    public function businesses()
    {
        $businesses = Business::with('user')->latest()->paginate(20);
        return view('admin.businesses', compact('businesses'));
    }

    public function approveBusiness(Business $business)
    {
        $business->update(['verified' => true]);
        return back()->with('success', 'Business approved.');
    }

    public function rejectBusiness(Business $business)
    {
        $business->update(['verified' => false]);
        return back()->with('success', 'Business rejected.');
    }

    public function coupons()
    {
        $coupons = Coupon::with('business')->latest()->paginate(20);
        return view('admin.coupons', compact('coupons'));
    }

    public function deleteCoupon(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }

    public function users()
    {
        $users = User::withCount('redemptions')->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function revenue()
    {
        $payments = Payment::with('business')->where('status', 'completed')->latest()->paginate(50);
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        return view('admin.revenue', compact('payments', 'totalRevenue'));
    }

    public function fraud()
    {
        $flags = FraudFlag::with(['user', 'reviewer'])->latest()->paginate(30);
        return view('admin.fraud', compact('flags'));
    }

    public function reviewFraud(FraudFlag $flag, Request $request)
    {
        $request->validate(['action' => 'required|in:clear,suspend']);
        $flag->update([
            'status' => $request->action === 'clear' ? 'cleared' : 'reviewed',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        if ($request->action === 'suspend' && $flag->user_id) {
            User::where('id', $flag->user_id)->update(['suspended' => true]);
        }
        return back()->with('success', 'Fraud flag reviewed.');
    }

    public function suspendUser(User $user)
    {
        $user->update(['suspended' => true]);
        return back()->with('success', 'User suspended.');
    }

    public function unsuspendUser(User $user)
    {
        $user->update(['suspended' => false]);
        return back()->with('success', 'User unsuspended.');
    }

    public function metrics()
    {
        $metrics = $this->analytics->getPlatformMetrics();
        $history = \App\Models\AnalyticsDaily::orderByDesc('date')->limit(30)->get();
        return view('admin.metrics', compact('metrics', 'history'));
    }

    public function partnerships()
    {
        $partners = BusinessPartner::with(['business', 'partner'])->latest()->paginate(30);
        $jointCoupons = Coupon::whereNotNull('partner_business_id')->with(['business', 'partnerBusiness'])->latest()->paginate(20);
        return view('admin.partnerships', compact('partners', 'jointCoupons'));
    }

    public function removePartnership(BusinessPartner $businessPartner)
    {
        $this->partnershipService->removePartnership($businessPartner->business, $businessPartner->partner);
        return back()->with('success', 'Partnership removed.');
    }
}
