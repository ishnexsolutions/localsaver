<x-app-layout title="Business Setup">
    <div class="max-w-lg mx-auto py-12">
        <div class="glass-card p-8">
            @if($step === 1)
                <h2 class="text-xl font-bold mb-2">📋 Complete your profile</h2>
                <p class="text-white/60 mb-6">Your business profile helps customers find you</p>
                <a href="{{ route('business.dashboard') }}" class="w-full btn-glow py-3 block text-center">Go to Dashboard</a>
                <form method="POST" action="{{ route('onboarding.business.complete') }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="step" value="1">
                    <button type="submit" class="w-full glass-card py-3">Skip — Complete later</button>
                </form>
            @elseif($step === 2)
                <h2 class="text-xl font-bold mb-2">🎫 Create your first coupon</h2>
                <p class="text-white/60 mb-6">Attract customers with an irresistible offer</p>
                <a href="{{ route('business.coupons.create') }}" class="w-full btn-glow py-3 block text-center">Create Coupon</a>
            @elseif($step === 3)
                <h2 class="text-xl font-bold mb-2">📈 Estimated reach</h2>
                <p class="text-white/60 mb-6">Your coupons could reach ~{{ number_format($estimatedReach ?? 0) }} users in your area</p>
                <p class="text-amber-200 text-sm mb-6">💡 Boost your coupon to appear first and get more redemptions!</p>
                <form method="POST" action="{{ route('onboarding.business.complete') }}">
                    @csrf
                    <input type="hidden" name="step" value="4">
                    <button type="submit" class="w-full btn-glow py-3">Finish Setup</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
