<x-app-layout title="Business Dashboard">
    <div class="space-y-6">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <h2 class="text-2xl font-bold">Dashboard</h2>
            <div class="flex gap-2">
                <a href="{{ route('business.partnerships.index') }}" class="glass-card px-4 py-2 hover:bg-white/15">🤝 Partnerships</a>
                <a href="{{ route('business.coupons.create') }}" class="btn-glow">+ New Coupon</a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Total Views</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_views'] ?? 0) }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Clicks</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_clicks'] ?? 0) }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Redemptions</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_redemptions'] ?? 0) }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Conversion Rate</p>
                <p class="text-2xl font-bold">{{ $stats['conversion_rate'] ?? 0 }}%</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">First-time Customers</p>
                <p class="text-2xl font-bold">{{ number_format($stats['first_time_customers'] ?? 0) }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Repeat Customers</p>
                <p class="text-2xl font-bold">{{ number_format($stats['repeat_customers'] ?? 0) }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Peak Redemption Hour</p>
                <p class="text-2xl font-bold">{{ isset($stats['peak_redemption_hour']) ? sprintf('%02d:00', $stats['peak_redemption_hour']) : '—' }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Unique Users</p>
                <p class="text-2xl font-bold">{{ number_format($stats['unique_users'] ?? 0) }}</p>
            </div>
        </div>
        @if(!empty($stats['suggestion']))
        <div class="glass-card p-4 border-amber-500/30">
            <p class="text-amber-200">💡 <strong>Suggestion:</strong> {{ $stats['suggestion'] }}</p>
        </div>
        @endif

        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Your Coupons</h3>
            @forelse($coupons as $coupon)
                <div class="flex flex-wrap justify-between items-center py-4 border-b border-white/10 last:border-0 gap-4">
                    <div>
                        <h4 class="font-medium">{{ $coupon->title }} @if($coupon->isJointCoupon())<span class="text-amber-400 text-xs">🤝 Joint</span>@endif</h4>
                        <p class="text-sm text-white/50">{{ $coupon->used_count }}/{{ $coupon->max_redemptions }} used · Expires {{ $coupon->expiry_date->format('M d') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('coupons.show', $coupon) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white/10 text-sm">View</a>
                        <a href="{{ route('business.coupons.edit', $coupon) }}" class="px-3 py-1.5 rounded-lg bg-white/10 text-sm">Edit</a>
                        <form method="POST" action="{{ route('business.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Delete this coupon?')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-300 text-sm">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-white/50 py-8 text-center">No coupons yet. Create your first one!</p>
            @endforelse
        </div>

        @if(isset($jointCoupons) && $jointCoupons->isNotEmpty())
        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Joint Coupons (Partner)</h3>
            @foreach($jointCoupons as $coupon)
                <div class="flex justify-between items-center py-3 border-b border-white/10 last:border-0">
                    <div>
                        <span class="font-medium">{{ $coupon->title }}</span>
                        <span class="text-amber-400 text-xs ml-2">with {{ $coupon->business->name }}</span>
                        <p class="text-sm text-white/50">{{ $coupon->used_count }}/{{ $coupon->max_redemptions }} used</p>
                    </div>
                    <a href="{{ route('coupons.show', $coupon) }}" class="px-3 py-1.5 rounded-lg bg-white/10 text-sm">View</a>
                </div>
            @endforeach
        </div>
        @endif

        @if(isset($partnershipOpportunities) && $partnershipOpportunities->isNotEmpty())
        <div class="glass-card p-6 border-violet-500/30">
            <h3 class="font-semibold mb-2">💡 Partnership Opportunities</h3>
            <p class="text-white/50 text-sm mb-3">Businesses your customers also visit</p>
            <div class="flex flex-wrap gap-2">
                @foreach($partnershipOpportunities as $opp)
                    <a href="{{ route('businesses.show', $opp->business) }}" class="px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-sm">
                        {{ $opp->business->name }} ({{ $opp->overlap_count }} shared)
                    </a>
                @endforeach
            </div>
            <a href="{{ route('business.partnerships.index') }}" class="inline-block mt-3 text-violet-400 text-sm">Manage partnerships →</a>
        </div>
        @endif
    </div>
</x-app-layout>
