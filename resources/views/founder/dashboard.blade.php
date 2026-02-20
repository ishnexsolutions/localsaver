<x-app-layout title="Founder Dashboard">
    <div class="space-y-6">
        <h2 class="text-2xl font-bold">Founder Analytics</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="glass-card p-5">
                <p class="text-white/50 text-sm">DAU</p>
                <p class="text-2xl font-bold">{{ $dau }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/50 text-sm">WAU</p>
                <p class="text-2xl font-bold">{{ $wau }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/50 text-sm">New Users Today</p>
                <p class="text-2xl font-bold">{{ $newUsers }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/50 text-sm">Redemptions Today</p>
                <p class="text-2xl font-bold">{{ $redemptionsToday }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/50 text-sm">Active Businesses</p>
                <p class="text-2xl font-bold">{{ $activeBusinesses }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/50 text-sm">Revenue Today</p>
                <p class="text-2xl font-bold text-emerald-400">₹{{ number_format($revenueToday, 0) }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/50 text-sm">Conversion %</p>
                <p class="text-2xl font-bold">{{ $conversionRate }}%</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/50 text-sm">Retention D7</p>
                <p class="text-2xl font-bold">{{ $retentionD7 }}%</p>
            </div>
        </div>
        @if($topCoupon)
        <div class="glass-card p-6">
            <h3 class="font-semibold mb-2">Top Coupon</h3>
            <p>{{ $topCoupon->title }} — {{ $topCoupon->business->name }} ({{ $topCoupon->used_count }} redemptions)</p>
        </div>
        @endif
        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Drop-off Funnel</h3>
            <div class="space-y-2">
                <div class="flex justify-between"><span>Signed up</span><span>{{ $dropOff['signed_up'] }}</span></div>
                <div class="flex justify-between"><span>With location</span><span>{{ $dropOff['with_location'] }}</span></div>
                <div class="flex justify-between"><span>Redeemed 1+</span><span>{{ $dropOff['redeemed_once'] }}</span></div>
                <div class="flex justify-between"><span>Redeemed 5+</span><span>{{ $dropOff['redeemed_5plus'] }}</span></div>
            </div>
        </div>
        @if($history->isNotEmpty())
        <div class="glass-card p-6 overflow-x-auto">
            <h3 class="font-semibold mb-4">14-Day History</h3>
            <table class="w-full">
                <thead><tr><th class="text-left p-2">Date</th><th class="text-left p-2">DAU</th><th class="text-left p-2">WAU</th><th class="text-left p-2">Redemptions</th><th class="text-left p-2">Revenue</th></tr></thead>
                <tbody>
                    @foreach($history as $d)
                        <tr class="border-t border-white/10"><td class="p-2">{{ $d->date->format('M d') }}</td><td class="p-2">{{ $d->dau }}</td><td class="p-2">{{ $d->wau }}</td><td class="p-2">{{ $d->redemptions_count }}</td><td class="p-2">₹{{ number_format($d->revenue, 0) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-app-layout>
