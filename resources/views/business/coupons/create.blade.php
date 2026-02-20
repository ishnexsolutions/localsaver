<x-app-layout title="Create Coupon">
    <div class="max-w-xl mx-auto">
        <a href="{{ route('business.dashboard') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white mb-6">← Back</a>
        <div class="glass-card p-6">
            <h2 class="text-xl font-bold mb-6">Create Coupon</h2>
            <form method="POST" action="{{ route('business.coupons.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-white/60 mb-2">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Description</label>
                    <textarea name="description" rows="3" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">{{ old('description') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-white/60 mb-2">Discount Value</label>
                        <input type="number" name="discount_value" value="{{ old('discount_value') }}" step="0.01" min="0" required
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm text-white/60 mb-2">Type</label>
                        <select name="discount_type" class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                            <option value="percentage" {{ old('discount_type', 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed (₹)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Max Redemptions</label>
                    <input type="number" name="max_redemptions" value="{{ old('max_redemptions', 100) }}" min="1" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-white/60 mb-2">Start Time (optional)</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}"
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm text-white/60 mb-2">End Time (optional)</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}"
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Radius (km)</label>
                    <input type="number" name="radius_km" value="{{ old('radius_km', 5) }}" step="0.5" min="0.5" max="50"
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                @if(isset($partners) && $partners->isNotEmpty())
                <div>
                    <label class="block text-sm text-white/60 mb-2">Joint Coupon (optional)</label>
                    <select name="partner_business_id" class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                        <option value="">Single business only</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ old('partner_business_id') == $partner->id ? 'selected' : '' }}>{{ $partner->name }} (Partner)</option>
                        @endforeach
                    </select>
                    <p class="text-white/40 text-xs mt-1">Joint coupons appear for both businesses. User must be within both locations to redeem.</p>
                </div>
                @endif
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="first_time_only" id="first_time" value="1" {{ old('first_time_only') ? 'checked' : '' }}
                        class="rounded bg-white/10 border-white/20">
                    <label for="first_time" class="text-sm text-white/60">First-time customers only</label>
                </div>
                <button type="submit" class="w-full btn-glow py-3">Create Coupon</button>
            </form>
        </div>
    </div>
</x-app-layout>
