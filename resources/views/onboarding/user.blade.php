<x-app-layout title="Welcome">
    <div class="max-w-lg mx-auto py-12">
        <div class="glass-card p-8">
            @if($step === 1)
                <h2 class="text-xl font-bold mb-2">📍 Confirm your location</h2>
                <p class="text-white/60 mb-6">We'll show you the best deals nearby</p>
                <button onclick="navigator.geolocation.getCurrentPosition(p => { document.getElementById('lat').value=p.coords.latitude; document.getElementById('lng').value=p.coords.longitude; document.getElementById('locForm').submit(); })" class="w-full btn-glow py-3 mb-4">Detect Location</button>
                <form id="locForm" method="POST" action="{{ route('onboarding.user.complete') }}">
                    @csrf
                    <input type="hidden" name="step" value="1">
                    <input type="hidden" name="lat" id="lat">
                    <input type="hidden" name="lng" id="lng">
                </form>
            @elseif($step === 2)
                <h2 class="text-xl font-bold mb-2">🎯 What are you interested in?</h2>
                <p class="text-white/60 mb-6">Select categories for personalized deals</p>
                <form method="POST" action="{{ route('onboarding.user.complete') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="step" value="2">
                    @foreach(\App\Models\Coupon::CATEGORIES as $key => $label)
                        <label class="flex items-center gap-3 p-4 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $key }}" class="rounded">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                    <button type="submit" class="w-full btn-glow py-3 mt-4">Continue</button>
                </form>
            @elseif($step === 3)
                <h2 class="text-xl font-bold mb-2">💰 Potential savings near you</h2>
                <p class="text-white/60 mb-6">Based on your location and interests</p>
                <div class="text-4xl font-bold text-emerald-400 mb-6">₹{{ number_format($potentialSavings ?? 0) }}+</div>
                <form method="POST" action="{{ route('onboarding.user.complete') }}">
                    @csrf
                    <input type="hidden" name="step" value="3">
                    <button type="submit" class="w-full btn-glow py-3">Continue</button>
                </form>
            @elseif($step === 4)
                <h2 class="text-xl font-bold mb-2">🎁 Your wallet is ready</h2>
                <p class="text-white/60 mb-6">Track every rupee you save. Redeem coupons to start earning!</p>
                <form method="POST" action="{{ route('onboarding.user.complete') }}">
                    @csrf
                    <input type="hidden" name="step" value="5">
                    <button type="submit" class="w-full btn-glow py-3">Get Started</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
