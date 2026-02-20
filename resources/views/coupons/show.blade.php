<x-app-layout :title="$coupon->title">
    <div class="max-w-2xl mx-auto">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white mb-6">← Back</a>

        <div class="glass-card p-6 mb-6">
            @if($coupon->is_boosted)
                <span class="inline-block px-3 py-1 text-sm font-medium bg-amber-500/30 text-amber-200 rounded-lg mb-4">⭐ Featured Deal</span>
            @endif
            <h1 class="text-2xl font-bold mb-2">{{ $coupon->title }}</h1>
            <p class="text-white/70 mb-4">{{ $coupon->description }}</p>
            <div class="flex flex-wrap gap-4 mb-4">
                <span class="text-3xl font-bold text-emerald-400">{{ $coupon->formatted_discount }}</span>
                @if($distance)
                    <span class="text-white/60">📍 {{ $distance }} km away</span>
                @endif
            </div>
            <div class="flex gap-4 text-sm text-white/50">
                <span>⏱ Expires {{ $coupon->expiry_date->format('M d, Y') }}</span>
                <span>{{ $coupon->max_redemptions - $coupon->used_count }} remaining</span>
            </div>
        </div>

        <div class="glass-card p-6 mb-6">
            @if($coupon->isJointCoupon())
                <span class="inline-block px-2 py-0.5 text-xs font-medium bg-amber-500/30 text-amber-200 rounded-lg mb-2">🤝 Joint Deal</span>
                <p class="text-white/50 text-sm mb-2">Valid at both locations</p>
            @endif
            <h3 class="font-semibold mb-2">{{ $coupon->business->name }}@if($coupon->partnerBusiness) + {{ $coupon->partnerBusiness->name }}@endif</h3>
            <p class="text-white/60 text-sm mb-4">{{ $coupon->business->address }}</p>
            @if(config('services.google_maps.api_key'))
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $coupon->business->lat }},{{ $coupon->business->lng }}" target="_blank"
                    class="btn-glow inline-flex items-center gap-2">
                    🗺️ Get Directions
                </a>
            @endif
        </div>

        @auth
            <div class="flex gap-2 mb-4">
                <form method="POST" action="{{ route('coupons.share', $coupon) }}" class="inline" id="share-form">
                    @csrf
                    <button type="button" onclick="shareCoupon()" class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-sm inline-flex items-center gap-2">
                        📤 Share
                    </button>
                </form>
            </div>
            @if($canRedeem)
                <form method="POST" action="{{ route('coupons.redeem', $coupon) }}" class="mb-6">
                    @csrf
                    <button type="submit" class="w-full btn-glow py-4 text-lg">
                        Redeem Coupon
                    </button>
                </form>
            @else
                <div class="glass-card p-4 border-amber-500/30 text-amber-200">
                    {{ $redeemMessage }}
                </div>
            @endif
        @else
            <a href="{{ route('login') }}" class="block w-full btn-glow py-4 text-center">Login to Redeem</a>
        @endauth

        @auth
        <details class="glass-card p-4 mt-4">
            <summary class="cursor-pointer text-sm text-white/60 hover:text-white">Report this coupon</summary>
            <form method="POST" action="{{ route('coupons.complain', $coupon) }}" class="mt-3 space-y-2">
                @csrf
                <select name="reason" required class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-sm">
                    <option value="">Select reason</option>
                    <option value="misleading">Misleading information</option>
                    <option value="fake_expired">Fake or expired deal</option>
                    <option value="not_honored">Business did not honor</option>
                    <option value="other">Other</option>
                </select>
                <textarea name="details" rows="2" placeholder="Optional details" class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-sm"></textarea>
                <button type="submit" class="btn-glow text-sm">Submit report</button>
            </form>
        </details>
        @endauth
    </div>

    @auth
    <script>
        function shareCoupon() {
            const url = window.location.href;
            const title = {!! json_encode($coupon->title . ' - ' . $coupon->formatted_discount) !!};
            if (navigator.share) {
                navigator.share({ title, url, text: title }).then(() => {
                    document.getElementById('share-form').submit();
                }).catch(() => copyAndSubmit(url));
            } else {
                copyAndSubmit(url);
            }
        }
        function copyAndSubmit(url) {
            navigator.clipboard.writeText(url).then(() => {
                document.getElementById('share-form').submit();
            });
        }
    </script>
    @endauth
</x-app-layout>
