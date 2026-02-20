<x-app-layout title="Edit Coupon">
    <div class="max-w-xl mx-auto">
        <a href="{{ route('business.dashboard') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white mb-6">← Back</a>
        <div class="glass-card p-6">
            <h2 class="text-xl font-bold mb-6">Edit Coupon</h2>
            <form method="POST" action="{{ route('business.coupons.update', $coupon) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm text-white/60 mb-2">Title</label>
                    <input type="text" name="title" value="{{ old('title', $coupon->title) }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Description</label>
                    <textarea name="description" rows="3" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">{{ old('description', $coupon->description) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-white/60 mb-2">Discount Value</label>
                        <input type="number" name="discount_value" value="{{ old('discount_value', $coupon->discount_value) }}" step="0.01" min="0" required
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm text-white/60 mb-2">Type</label>
                        <select name="discount_type" class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                            <option value="percentage" {{ $coupon->discount_type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ $coupon->discount_type === 'fixed' ? 'selected' : '' }}>Fixed (₹)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Max Redemptions (min: {{ $coupon->used_count }})</label>
                    <input type="number" name="max_redemptions" value="{{ old('max_redemptions', $coupon->max_redemptions) }}" min="{{ $coupon->used_count }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date', $coupon->expiry_date->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-white/60 mb-2">Start Time (optional)</label>
                        <input type="time" name="start_time" value="{{ old('start_time', $coupon->start_time ? (\Carbon\Carbon::parse($coupon->start_time)->format('H:i')) : '') }}"
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm text-white/60 mb-2">End Time (optional)</label>
                        <input type="time" name="end_time" value="{{ old('end_time', $coupon->end_time ? (\Carbon\Carbon::parse($coupon->end_time)->format('H:i')) : '') }}"
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Radius (km)</label>
                    <input type="number" name="radius_km" value="{{ old('radius_km', $coupon->radius_km) }}" step="0.5" min="0.5" max="50"
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="first_time_only" id="first_time" value="1" {{ ($coupon->first_time_only || old('first_time_only')) ? 'checked' : '' }}
                        class="rounded bg-white/10 border-white/20">
                    <label for="first_time" class="text-sm text-white/60">First-time customers only</label>
                </div>
                <button type="submit" class="w-full btn-glow py-3">Update Coupon</button>
            </form>
        </div>
    </div>
</x-app-layout>
