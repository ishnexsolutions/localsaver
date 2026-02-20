<x-app-layout title="Discover Businesses">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold">Discover Businesses</h1>

        <div x-data="locationPicker()" class="glass-card p-4">
            <div class="flex flex-wrap gap-4 items-center justify-between">
                <button @click="getLocation()" :disabled="loading" class="btn-glow flex items-center gap-2">
                    <span x-show="!loading">📍</span>
                    <span x-show="loading" class="animate-spin">⏳</span>
                    <span x-text="locationSet ? 'Update Location' : 'Detect Location'"></span>
                </button>
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('businesses.index', array_merge(request()->query(), ['category' => null])) }}"
                        class="px-4 py-2 rounded-xl {{ !$category ? 'bg-violet-500/30 border border-violet-400/50' : 'bg-white/5 border border-white/20 hover:border-white/30' }}">All</a>
                    @foreach(\App\Models\Coupon::CATEGORIES as $key => $label)
                        <a href="{{ route('businesses.index', array_merge(request()->query(), ['category' => $key])) }}"
                            class="px-4 py-2 rounded-xl {{ $category === $key ? 'bg-violet-500/30 border border-violet-400/50' : 'bg-white/5 border border-white/20 hover:border-white/30' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        @if(isset($trending) && $trending->isNotEmpty())
        <div>
            <h3 class="text-lg font-semibold mb-3">🔥 Trending Businesses</h3>
            <div class="flex gap-4 overflow-x-auto pb-2">
                @foreach($trending as $b)
                    <a href="{{ route('businesses.show', $b) }}" class="flex-shrink-0 w-56">
                        <div class="glass-card p-4 glass-card-hover h-full">
                            <h4 class="font-medium">{{ $b->name }}</h4>
                            <p class="text-white/50 text-sm">{{ \App\Models\Coupon::CATEGORIES[$b->category] ?? $b->category }}</p>
                            <p class="text-emerald-400 text-xs mt-2">{{ $b->coupons_count ?? 0 }} active coupons</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <div>
            <h3 class="text-lg font-semibold mb-3">Nearby Businesses</h3>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse($businesses as $business)
                    <a href="{{ route('businesses.show', $business) }}" class="block group">
                        <div class="glass-card p-5 glass-card-hover h-full">
                            <h3 class="font-semibold text-lg group-hover:text-violet-300 transition">{{ $business->name }}</h3>
                            <p class="text-white/60 text-sm mt-1">{{ \App\Models\Coupon::CATEGORIES[$business->category] ?? $business->category }}</p>
                            <div class="mt-3 flex items-center justify-between text-sm">
                                <span class="text-white/50">{{ $business->coupons_count ?? 0 }} coupons</span>
                                @if(isset($business->distance))
                                    <span class="text-white/50">📍 {{ round($business->distance, 1) }} km</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full glass-card p-12 text-center">
                        <p class="text-white/60">No businesses found. Enable location or try a different category.</p>
                    </div>
                @endforelse
            </div>
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
                    navigator.geolocation.getCurrentPosition(
                        (pos) => { window.location.href = '{{ route("businesses.index") }}?lat=' + pos.coords.latitude + '&lng=' + pos.coords.longitude + '&category={{ $category ?? "" }}'; },
                        () => { alert('Unable to get location'); this.loading = false; }
                    );
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
