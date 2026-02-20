<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessPartner;
use App\Models\PartnershipRequest;

class PartnershipService
{
    public function sendRequest(Business $requester, Business $target, ?string $message = null): PartnershipRequest
    {
        if ($requester->id === $target->id) {
            throw new \InvalidArgumentException('Cannot partner with yourself.');
        }

        $exists = PartnershipRequest::where('requester_business_id', $requester->id)
            ->where('target_business_id', $target->id)
            ->first();

        if ($exists) {
            if ($exists->status === 'pending') {
                throw new \InvalidArgumentException('Partnership request already pending.');
            }
            $exists->update(['status' => 'pending', 'message' => $message, 'responded_at' => null]);
            return $exists;
        }

        if ($this->arePartners($requester, $target)) {
            throw new \InvalidArgumentException('Already partnered.');
        }

        return PartnershipRequest::create([
            'requester_business_id' => $requester->id,
            'target_business_id' => $target->id,
            'message' => $message,
            'status' => 'pending',
        ]);
    }

    public function acceptRequest(PartnershipRequest $request): void
    {
        if ($request->status !== 'pending') {
            throw new \InvalidArgumentException('Request already responded.');
        }

        $request->update(['status' => 'accepted', 'responded_at' => now()]);

        BusinessPartner::firstOrCreate(
            ['business_id' => $request->requester_business_id, 'partner_business_id' => $request->target_business_id]
        );
        BusinessPartner::firstOrCreate(
            ['business_id' => $request->target_business_id, 'partner_business_id' => $request->requester_business_id]
        );
    }

    public function rejectRequest(PartnershipRequest $request): void
    {
        $request->update(['status' => 'rejected', 'responded_at' => now()]);
    }

    public function removePartnership(Business $business, Business $partner): void
    {
        BusinessPartner::where(function ($q) use ($business, $partner) {
            $q->where('business_id', $business->id)->where('partner_business_id', $partner->id);
        })->orWhere(function ($q) use ($business, $partner) {
            $q->where('business_id', $partner->id)->where('partner_business_id', $business->id);
        })->delete();
    }

    public function arePartners(Business $a, Business $b): bool
    {
        return BusinessPartner::where('business_id', $a->id)
            ->where('partner_business_id', $b->id)
            ->exists();
    }

    public function canCreateJointCoupon(Business $business, Business $partner): bool
    {
        return $this->arePartners($business, $partner);
    }
}
