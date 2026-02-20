<x-app-layout title="System Health">
    <div class="space-y-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white">← Back</a>
        <h2 class="text-2xl font-bold">System Health</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-2">Database</h3>
                <p class="{{ $checks['database']['status'] === 'ok' ? 'text-emerald-400' : 'text-red-400' }}">{{ $checks['database']['message'] }}</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-2">Cache</h3>
                <p class="{{ $checks['cache']['status'] === 'ok' ? 'text-emerald-400' : 'text-red-400' }}">{{ $checks['cache']['message'] }}</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-2">Queue</h3>
                <p class="text-emerald-400">{{ $checks['queue']['message'] }}</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-2">Fraud Alerts (Pending)</h3>
                <p class="{{ $checks['fraud_alerts'] > 0 ? 'text-amber-400' : 'text-emerald-400' }}">{{ $checks['fraud_alerts'] }}</p>
            </div>
        </div>
        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Launch Safety</h3>
            <div class="space-y-2">
                <p>Empty categories: {{ $checks['empty_categories']['count'] }} {{ $checks['empty_categories']['count'] > 0 ? '— ' . implode(', ', $checks['empty_categories']['categories']) : '' }}</p>
                <p>Weak coupons (score &lt; 40): {{ $checks['weak_coupons'] }}</p>
                <p>Inactive businesses (no active coupons): {{ $checks['inactive_businesses'] }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
