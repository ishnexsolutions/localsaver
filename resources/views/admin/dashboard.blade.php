<x-app-layout title="Admin Dashboard">
    <div class="space-y-6">
        <h2 class="text-2xl font-bold">Admin Dashboard</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Users</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_users']) }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Businesses</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_businesses']) }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Coupons</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_coupons']) }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Revenue</p>
                <p class="text-2xl font-bold text-emerald-400">₹{{ number_format($stats['total_revenue'], 0) }}</p>
            </div>
        </div>

        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Pending Business Approvals</h3>
            @forelse($pendingBusinesses as $business)
                <div class="flex justify-between items-center py-4 border-b border-white/10 last:border-0">
                    <div>
                        <p class="font-medium">{{ $business->name }}</p>
                        <p class="text-sm text-white/50">{{ $business->user->email }} · {{ $business->category }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.businesses.approve', $business) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500/30 text-emerald-300 text-sm">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.businesses.reject', $business) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/30 text-red-300 text-sm">Reject</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-white/50 py-4">No pending approvals</p>
            @endforelse
        </div>

        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Platform Metrics</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-white/50 text-sm">DAU</p>
                    <p class="font-bold">{{ $metrics['dau'] ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-white/50 text-sm">Conversion %</p>
                    <p class="font-bold">{{ $metrics['conversion_rate'] ?? 0 }}%</p>
                </div>
                <div>
                    <p class="text-white/50 text-sm">Retention D7</p>
                    <p class="font-bold">{{ $metrics['retention_d7'] ?? 0 }}%</p>
                </div>
                <div>
                    <p class="text-white/50 text-sm">Pending Fraud</p>
                    <p class="font-bold {{ ($pendingFraudCount ?? 0) > 0 ? 'text-amber-400' : '' }}">{{ $pendingFraudCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="flex gap-4 flex-wrap">
            <a href="{{ route('admin.businesses') }}" class="glass-card px-6 py-3 hover:bg-white/15 transition">Businesses</a>
            <a href="{{ route('admin.coupons') }}" class="glass-card px-6 py-3 hover:bg-white/15 transition">Coupons</a>
            <a href="{{ route('admin.users') }}" class="glass-card px-6 py-3 hover:bg-white/15 transition">Users</a>
            <a href="{{ route('admin.revenue') }}" class="glass-card px-6 py-3 hover:bg-white/15 transition">Revenue</a>
            <a href="{{ route('admin.fraud') }}" class="glass-card px-6 py-3 hover:bg-white/15 transition">Fraud Flags</a>
            <a href="{{ route('admin.metrics') }}" class="glass-card px-6 py-3 hover:bg-white/15 transition">Metrics</a>
            <a href="{{ route('admin.partnerships') }}" class="glass-card px-6 py-3 hover:bg-white/15 transition">Partnerships</a>
            <a href="{{ route('founder.dashboard') }}" class="glass-card px-6 py-3 hover:bg-white/15 transition">Founder</a>
            <a href="{{ route('admin.health') }}" class="glass-card px-6 py-3 hover:bg-white/15 transition">Health</a>
        </div>
    </div>
</x-app-layout>
