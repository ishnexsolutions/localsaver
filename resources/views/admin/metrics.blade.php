<x-app-layout title="Platform Metrics">
    <div class="space-y-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white">← Back</a>
        <h2 class="text-2xl font-bold">Platform Metrics</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">DAU</p>
                <p class="text-2xl font-bold">{{ $metrics['dau'] ?? 0 }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">WAU</p>
                <p class="text-2xl font-bold">{{ $metrics['wau'] ?? 0 }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Total Redemptions</p>
                <p class="text-2xl font-bold">{{ number_format($metrics['total_redemptions'] ?? 0) }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Conversion Rate</p>
                <p class="text-2xl font-bold">{{ $metrics['conversion_rate'] ?? 0 }}%</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Total Revenue</p>
                <p class="text-2xl font-bold text-emerald-400">₹{{ number_format($metrics['total_revenue'] ?? 0) }}</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Retention D1</p>
                <p class="text-2xl font-bold">{{ $metrics['retention_d1'] ?? 0 }}%</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Retention D7</p>
                <p class="text-2xl font-bold">{{ $metrics['retention_d7'] ?? 0 }}%</p>
            </div>
            <div class="glass-card p-5">
                <p class="text-white/60 text-sm">Retention D30</p>
                <p class="text-2xl font-bold">{{ $metrics['retention_d30'] ?? 0 }}%</p>
            </div>
        </div>
        @if($history->isNotEmpty())
        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Daily History</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="text-left p-3">Date</th>
                            <th class="text-left p-3">DAU</th>
                            <th class="text-left p-3">WAU</th>
                            <th class="text-left p-3">Redemptions</th>
                            <th class="text-left p-3">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $day)
                            <tr class="border-t border-white/10">
                                <td class="p-3">{{ $day->date->format('M d, Y') }}</td>
                                <td class="p-3">{{ $day->dau }}</td>
                                <td class="p-3">{{ $day->wau }}</td>
                                <td class="p-3">{{ $day->redemptions_count }}</td>
                                <td class="p-3">₹{{ number_format($day->revenue, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
