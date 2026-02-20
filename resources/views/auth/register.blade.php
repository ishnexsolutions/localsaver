<x-app-layout title="Sign Up">
    <div class="max-w-md mx-auto py-12">
        <div class="glass-card p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Create Account</h2>
            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf
                @if(request('ref'))
                    <input type="hidden" name="referral_code" value="{{ request('ref') }}">
                @endif
                <div>
                    <label class="block text-sm text-white/60 mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 focus:ring-2 focus:ring-violet-400/20 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 focus:ring-2 focus:ring-violet-400/20 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Phone (optional)</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="10-digit"
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 focus:ring-2 focus:ring-violet-400/20 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 focus:ring-2 focus:ring-violet-400/20 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 focus:ring-2 focus:ring-violet-400/20 outline-none transition">
                </div>
                <button type="submit" class="w-full btn-glow py-3">Sign Up</button>
            </form>
            <p class="mt-4 text-center text-white/60 text-sm">
                Already have an account? <a href="{{ route('login') }}" class="text-violet-400">Login</a>
            </p>
        </div>

        <div class="mt-6 glass-card p-6 text-center">
            <p class="text-white/60 mb-3">Have a business? List your coupons for ₹199</p>
            <a href="{{ route('business.register') }}" class="btn-glow inline-block">Register as Business</a>
        </div>
    </div>
</x-app-layout>
