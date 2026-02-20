<x-app-layout :title="$business->name">
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="glass-card p-6">
            <div class="flex flex-wrap justify-between items-start gap-4">
                <div>
                    <h1 class="text-2xl font-bold">{{ $business->name }}</h1>
                    <p class="text-white/60 mt-1">{{ \App\Models\Coupon::CATEGORIES[$business->category] ?? $business->category }}</p>
                    <p class="text-white/50 text-sm mt-2">{{ $business->address }}</p>
                    <div class="flex flex-wrap gap-3 mt-3">
                        @if($trustScore !== null)
                            <span class="px-2 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 text-sm">Trust: {{ number_format($trustScore, 1) }}</span>
                        @endif
                        @if($business->verified)
                            <span class="px-2 py-1 rounded-lg bg-blue-500/20 text-blue-300 text-sm">✓ Verified</span>
                        @endif
                        <span class="px-2 py-1 rounded-lg bg-white/10 text-sm">Popularity: {{ $business->popularity_score ?? 0 }}</span>
                        <span class="px-2 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 text-sm">{{ number_format($totalRedemptions) }} redemptions</span>
                        @if($avgRating)
                            <span class="px-2 py-1 rounded-lg bg-amber-500/20 text-amber-200 text-sm">⭐ {{ number_format($avgRating, 1) }} ({{ $ratingCount }})</span>
                        @endif
                        @if($business->partners->isNotEmpty())
                            <span class="px-2 py-1 rounded-lg bg-amber-500/20 text-amber-200 text-sm">🤝 Partner</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($business->partners->isNotEmpty())
        <div class="glass-card p-6">
            <h3 class="font-semibold mb-3">Partner Businesses</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($business->partners as $partner)
                    <a href="{{ route('businesses.show', $partner) }}" class="px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-sm">
                        {{ $partner->name }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        @auth
        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Rate this business</h3>
            <form method="POST" action="{{ route('businesses.rate', $business) }}">
                @csrf
                <div class="flex gap-2 mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer text-2xl text-amber-400/50 hover:text-amber-400"><input type="radio" name="rating" value="{{ $i }}" {{ ($userRating->rating ?? 0) == $i ? 'checked' : '' }} class="sr-only">★</label>
                    @endfor
                </div>
                <textarea name="comment" rows="2" placeholder="Optional comment" class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 mb-2 text-sm">{{ $userRating->comment ?? '' }}</textarea>
                <button type="submit" class="btn-glow text-sm">Submit rating</button>
            </form>
        </div>
        @endauth

        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Active Coupons</h3>
            @forelse($business->coupons as $coupon)
                <a href="{{ route('coupons.show', $coupon) }}" class="block py-4 border-b border-white/10 last:border-0 hover:bg-white/5 -mx-4 px-4 rounded-lg transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="font-medium">{{ $coupon->title }}</span>
                            @if($coupon->isJointCoupon())
                                <span class="text-xs text-amber-400 ml-2">🤝 Joint</span>
                            @endif
                            <p class="text-white/50 text-sm">{{ $coupon->formatted_discount }}</p>
                        </div>
                        <span class="text-white/50 text-sm">{{ $coupon->used_count }}/{{ $coupon->max_redemptions }}</span>
                    </div>
                </a>
            @empty
                <p class="text-white/50">No active coupons.</p>
            @endforelse
        </div>

        @if($customersAlsoVisit->isNotEmpty())
        <div class="glass-card p-6">
            <h3 class="font-semibold mb-3">Customers Also Visit</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($customersAlsoVisit as $item)
                    @if($item->business ?? null)
                        <a href="{{ route('businesses.show', $item->business) }}" class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-sm">
                            {{ $item->business->name }} <span class="text-white/50">({{ $item->overlap_count }})</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
