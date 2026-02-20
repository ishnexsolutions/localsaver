<x-app-layout title="Business Registration">
    <div class="max-w-md mx-auto py-12">
        <div class="glass-card p-8">
            <h2 class="text-2xl font-bold mb-2 text-center">Register Your Business</h2>
            <p class="text-white/60 text-sm text-center mb-6">One-time fee of ₹199 to activate your business dashboard</p>

            <form method="POST" action="{{ route('business.register.store') }}" class="space-y-4" x-data="{ loading: false }">
                @csrf
                <div>
                    <label class="block text-sm text-white/60 mb-2">Your Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <hr class="border-white/10">
                <div>
                    <label class="block text-sm text-white/60 mb-2">Business Name</label>
                    <input type="text" name="business_name" value="{{ old('business_name') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Category</label>
                    <select name="category" required class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                        @foreach(\App\Models\Coupon::CATEGORIES as $key => $label)
                            <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-white/60 mb-2">Latitude</label>
                        <input type="number" step="any" name="lat" id="lat" value="{{ old('lat') }}" required
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm text-white/60 mb-2">Longitude</label>
                        <input type="number" step="any" name="lng" id="lng" value="{{ old('lng') }}" required
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                    </div>
                </div>
                <button type="button" onclick="navigator.geolocation.getCurrentPosition(p => { document.getElementById('lat').value=p.coords.latitude; document.getElementById('lng').value=p.coords.longitude; })"
                    class="text-sm text-violet-400">📍 Use my location</button>

                <button type="submit" class="w-full btn-glow py-3">Continue to Payment (₹199)</button>
            </form>
        </div>
    </div>
</x-app-layout>
