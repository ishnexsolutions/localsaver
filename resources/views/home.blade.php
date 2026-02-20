<x-app-layout title="Deals Near You">
    <div x-data="locationPicker()" class="space-y-6">
        {{-- Hero --}}
        <div class="text-center py-8">
            <h1 class="text-4xl md:text-5xl font-bold mb-3 bg-gradient-to-r from-white via-violet-200 to-purple-400 bg-clip-text text-transparent">
                Deals Near You
            </h1>
            <p class="text-white/60 text-lg">Discover exclusive coupons from businesses around you</p>

            @guest
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('login') }}" class="btn-glow">Login with Phone</a>
                    <a href="{{ route('register') }}" class="glass-card px-6 py-2.5 rounded-xl border-white/20 hover:border-violet-400/30 transition">Email Sign Up</a>
                </div>
            @endguest
        </div>

        {{-- Location & Filters --}}
        <div class="glass-card p-4 rounded-2xl">
            <div class="flex flex-wrap gap-4 items-center justify-between">
                <button @click="getLocation()" :disabled="loading"
                    class="btn-glow flex items-center gap-2">
                    <span x-show="!loading">📍</span>
                    <span x-show="loading" class="animate-spin">⏳</span>
                    <span x-text="locationSet ? 'Update Location' : 'Detect Location'"></span>
                </button>
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('home', array_merge(request()->query(), ['category' => null])) }}"
                        class="px-4 py-2 rounded-xl {{ !$category ? 'bg-violet-500/30 border border-violet-400/50' : 'bg-white/5 border border-white/20 hover:border-white/30' }}">
                        All
                    </a>
                    @foreach(\App\Models\Coupon::CATEGORIES as $key => $label)
                        <a href="{{ route('home', array_merge(request()->query(), ['category' => $key])) }}"
                            class="px-4 py-2 rounded-xl {{ $category === $key ? 'bg-violet-500/30 border border-violet-400/50' : 'bg-white/5 border border-white/20 hover:border-white/30' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Trending Near You --}}
        @if(isset($trending) && $trending->isNotEmpty())
        <div>
            <h3 class="text-lg font-semibold mb-3">🔥 Trending Near You</h3>
            <div class="flex gap-4 overflow-x-auto pb-2">
                @foreach($trending as $coupon)
                    <a href="{{ route('coupons.show', $coupon) }}" class="flex-shrink-0 w-64">
                        <div class="glass-card p-4 glass-card-hover h-full">
                            <h4 class="font-medium">{{ $coupon->title }}</h4>
                            <p class="text-emerald-400 text-sm mt-1">{{ $coupon->formatted_discount }}</p>
                            <p class="text-white/50 text-xs mt-1">{{ $coupon->business->name ?? '' }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Coupon Grid --}}
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($coupons as $coupon)
                <a href="{{ route('coupons.show', $coupon) }}" class="block group">
                    <div class="glass-card p-5 glass-card-hover h-full">
                        @if($coupon->is_boosted)
                            <span class="inline-block px-2 py-0.5 text-xs font-medium bg-amber-500/30 text-amber-200 rounded-lg mb-3">⭐ Featured</span>
                        @endif
                        @if($coupon->isJointCoupon())
                            <span class="inline-block px-2 py-0.5 text-xs font-medium bg-violet-500/30 text-violet-200 rounded-lg mb-3">🤝 Joint</span>
                        @endif
                        <h3 class="font-semibold text-lg mb-1 group-hover:text-violet-300 transition">{{ $coupon->title }}</h3>
                        <p class="text-white/60 text-sm mb-3 line-clamp-2">{{ $coupon->description }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-emerald-400">₹{{ $coupon->discount_value }}{{ $coupon->discount_type === 'percentage' ? '%' : '' }} OFF</span>
                            <span class="text-sm text-white/50">{{ $coupon->used_count }}/{{ $coupon->max_redemptions }} used</span>
                        </div>
                        <div class="mt-3 flex items-center gap-2 text-sm text-white/50">
                            @if(isset($coupon->distance))
                                <span>📍 {{ $coupon->distance }} km</span>
                            @endif
                            <span>⏱ {{ $coupon->expiry_date->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full glass-card p-12 text-center">
                    <p class="text-white/60 text-lg">No deals in your area yet.</p>
                    <p class="text-white/40 mt-2">Enable location to see nearby coupons, or try a different category.</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        function locationPicker() {
            return {
                loading: false,
                locationSet: {{ ($lat && $lng) ? 'true' : 'false' }},
                getLocation() {
                    this.loading = true;
                    if (!navigator.geolocation) {
                        alert('Geolocation not supported');
                        this.loading = false;
                        return;
                    }
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const lat = pos.coords.latitude;
                            const lng = pos.coords.longitude;
                            window.location.href = '{{ route("home") }}?lat=' + lat + '&lng=' + lng + '&category={{ $category ?? "" }}';
                        },
                        () => {
                            alert('Unable to get location');
                            this.loading = false;
                        }
                    );
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
