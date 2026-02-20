<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PartnershipRequest;
use App\Services\PartnershipService;
use App\Services\BusinessNetworkAnalyticsService;
use Illuminate\Http\Request;

class PartnershipController extends Controller
{
    public function __construct(
        private PartnershipService $partnershipService,
        private BusinessNetworkAnalyticsService $analyticsService
    ) {}

    public function index()
    {
        $business = auth()->user()->business;
        if (!$business->activated) {
            return redirect()->route('business.payment');
        }

        $partners = $business->partners()->withCount(['coupons' => fn ($q) => $q->where('status', 'active')])->get();
        $requestsSent = $business->partnershipRequestsSent()->with('target')->where('status', 'pending')->get();
        $requestsReceived = $business->partnershipRequestsReceived()->with('requester')->where('status', 'pending')->get();
        $opportunities = $this->analyticsService->getPartnershipOpportunities($business);

        return view('business.partnerships.index', compact('partners', 'requestsSent', 'requestsReceived', 'opportunities'));
    }

    public function sendRequest(Request $request)
    {
        $request->validate([
            'target_business_id' => 'required|exists:businesses,id',
            'message' => 'nullable|string|max:500',
        ]);

        $business = auth()->user()->business;
        $target = Business::findOrFail($request->target_business_id);

        try {
            $this->partnershipService->sendRequest($business, $target, $request->message);
            return back()->with('success', 'Partnership request sent.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function acceptRequest(PartnershipRequest $partnershipRequest)
    {
        $business = auth()->user()->business;
        if ($partnershipRequest->target_business_id !== $business->id) {
            abort(403);
        }

        try {
            $this->partnershipService->acceptRequest($partnershipRequest);
            return back()->with('success', 'Partnership accepted!');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectRequest(PartnershipRequest $partnershipRequest)
    {
        $business = auth()->user()->business;
        if ($partnershipRequest->target_business_id !== $business->id) {
            abort(403);
        }

        $this->partnershipService->rejectRequest($partnershipRequest);
        return back()->with('success', 'Request rejected.');
    }

    public function removePartner(Business $partner)
    {
        $business = auth()->user()->business;
        if (!$this->partnershipService->arePartners($business, $partner)) {
            abort(404);
        }

        $this->partnershipService->removePartnership($business, $partner);
        return back()->with('success', 'Partnership removed.');
    }
}
