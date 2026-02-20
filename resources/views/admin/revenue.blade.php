<x-app-layout title="Revenue">
    <div class="space-y-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white">← Back</a>
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">Revenue</h2>
            <p class="text-2xl font-bold text-emerald-400">₹{{ number_format($totalRevenue, 0) }}</p>
        </div>
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="text-left p-4">Business</th>
                            <th class="text-left p-4">Amount</th>
                            <th class="text-left p-4">Type</th>
                            <th class="text-left p-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr class="border-t border-white/10">
                                <td class="p-4">{{ $payment->business->name }}</td>
                                <td class="p-4">₹{{ $payment->amount }}</td>
                                <td class="p-4">{{ $payment->type }}</td>
                                <td class="p-4">{{ $payment->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $payments->links() }}
    </div>
</x-app-layout>
