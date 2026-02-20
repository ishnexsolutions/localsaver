<x-app-layout title="Coupons">
    <div class="space-y-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white">← Back</a>
        <h2 class="text-2xl font-bold">Coupons</h2>
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="text-left p-4">Title</th>
                            <th class="text-left p-4">Business</th>
                            <th class="text-left p-4">Used</th>
                            <th class="text-left p-4">Expiry</th>
                            <th class="text-left p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                            <tr class="border-t border-white/10">
                                <td class="p-4">{{ $coupon->title }}</td>
                                <td class="p-4">{{ $coupon->business->name }}</td>
                                <td class="p-4">{{ $coupon->used_count }}/{{ $coupon->max_redemptions }}</td>
                                <td class="p-4">{{ $coupon->expiry_date->format('M d') }}</td>
                                <td class="p-4">
                                    <form method="POST" action="{{ route('admin.coupons.delete', $coupon) }}" class="inline" onsubmit="return confirm('Delete?')">
                                        @csrf
                                        <button type="submit" class="text-red-400 text-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $coupons->links() }}
    </div>
</x-app-layout>
