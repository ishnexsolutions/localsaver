<x-app-layout title="Partnerships">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">Partnerships</h2>
            <a href="{{ route('businesses.index') }}" class="text-violet-400 hover:text-violet-300">Discover Businesses →</a>
        </div>

        @if($opportunities->isNotEmpty())
        <div class="glass-card p-6">
            <h3 class="font-semibold mb-3">💡 Partnership Opportunities (Customers also visit)</h3>
            <div class="space-y-3">
                @foreach($opportunities as $opp)
                    <div class="flex justify-between items-center py-2 border-b border-white/10 last:border-0">
                        <div>
                            <a href="{{ route('businesses.show', $opp->business) }}" class="font-medium hover:text-violet-300">{{ $opp->business->name }}</a>
                            <p class="text-white/50 text-sm">{{ $opp->overlap_count }} shared customers</p>
                        </div>
                        <form method="POST" action="{{ route('business.partnerships.request') }}" class="inline">
                            @csrf
                            <input type="hidden" name="target_business_id" value="{{ $opp->business->id }}">
                            <button type="submit" class="btn-glow text-sm py-1.5 px-3">Send Request</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($requestsReceived->isNotEmpty())
        <div class="glass-card p-6">
            <h3 class="font-semibold mb-3">Incoming Requests</h3>
            @foreach($requestsReceived as $req)
                <div class="flex justify-between items-center py-4 border-b border-white/10">
                    <div>
                        <a href="{{ route('businesses.show', $req->requester) }}" class="font-medium">{{ $req->requester->name }}</a>
                        @if($req->message)
                            <p class="text-white/50 text-sm mt-1">{{ $req->message }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('business.partnerships.accept', $req) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500/30 text-emerald-300 text-sm">Accept</button>
                        </form>
                        <form method="POST" action="{{ route('business.partnerships.reject', $req) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-300 text-sm">Reject</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Your Partners ({{ $partners->count() }})</h3>
            @forelse($partners as $partner)
                <div class="flex justify-between items-center py-4 border-b border-white/10 last:border-0">
                    <div>
                        <a href="{{ route('businesses.show', $partner) }}" class="font-medium">{{ $partner->name }}</a>
                        <span class="text-amber-400 text-sm ml-2">🤝 Partner badge</span>
                        <p class="text-white/50 text-sm">{{ $partner->coupons_count ?? 0 }} active coupons</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('businesses.show', $partner) }}" class="px-3 py-1.5 rounded-lg bg-white/10 text-sm">View</a>
                        <form method="POST" action="{{ route('business.partnerships.remove', $partner) }}" class="inline" onsubmit="return confirm('Remove this partnership?')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-300 text-sm">Remove</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-white/50">No partners yet. Discover businesses with overlapping customers and send partnership requests.</p>
            @endforelse
        </div>

        @if($requestsSent->isNotEmpty())
        <div class="glass-card p-6">
            <h3 class="font-semibold mb-3">Pending Requests Sent</h3>
            @foreach($requestsSent as $req)
                <div class="flex justify-between items-center py-3 border-b border-white/10">
                    <span>{{ $req->target->name }}</span>
                    <span class="text-amber-400 text-sm">Pending</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</x-app-layout>
