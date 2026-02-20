<x-app-layout title="Partnerships">
    <div class="space-y-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white">← Back</a>
        <h2 class="text-2xl font-bold">Partnerships</h2>

        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">All Partnerships</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="text-left p-4">Business</th>
                            <th class="text-left p-4">Partner</th>
                            <th class="text-left p-4">Created</th>
                            <th class="text-left p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partners as $bp)
                            <tr class="border-t border-white/10">
                                <td class="p-4">{{ $bp->business->name }}</td>
                                <td class="p-4">{{ $bp->partner->name }}</td>
                                <td class="p-4">{{ $bp->created_at->format('M d, Y') }}</td>
                                <td class="p-4">
                                    <form method="POST" action="{{ route('admin.partnerships.remove', $bp) }}" class="inline" onsubmit="return confirm('Remove this partnership?')">
                                        @csrf
                                        <button type="submit" class="text-red-400 text-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $partners->links() }}
        </div>

        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Joint Coupons</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="text-left p-4">Coupon</th>
                            <th class="text-left p-4">Primary Business</th>
                            <th class="text-left p-4">Partner Business</th>
                            <th class="text-left p-4">Used</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jointCoupons as $coupon)
                            <tr class="border-t border-white/10">
                                <td class="p-4">{{ $coupon->title }}</td>
                                <td class="p-4">{{ $coupon->business->name }}</td>
                                <td class="p-4">{{ $coupon->partnerBusiness->name ?? '—' }}</td>
                                <td class="p-4">{{ $coupon->used_count }}/{{ $coupon->max_redemptions }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $jointCoupons->links() }}
        </div>
    </div>
</x-app-layout>
