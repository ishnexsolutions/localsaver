<x-app-layout title="Login">
    <div class="max-w-md mx-auto py-12">
        <div class="glass-card p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Login to LocalSaver</h2>

            @if(session('otp_sent'))
                <form method="POST" action="{{ route('login.verify') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="phone" value="{{ session('phone') }}">
                    <p class="text-white/60 text-sm">Enter the 6-digit OTP sent to {{ session('phone') }}</p>
                    <input type="text" name="code" maxlength="6" pattern="\d{6}" placeholder="000000"
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 focus:ring-2 focus:ring-violet-400/20 outline-none transition"
                        autofocus required>
                    <button type="submit" class="w-full btn-glow py-3">Verify & Login</button>
                </form>
            @else
                <form method="POST" action="{{ route('login.otp') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm text-white/60 mb-2">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="10-digit mobile number"
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 focus:ring-2 focus:ring-violet-400/20 outline-none transition"
                            maxlength="10" pattern="\d{10}" required>
                    </div>
                    <button type="submit" class="w-full btn-glow py-3">Send OTP</button>
                </form>
            @endif

            <div class="mt-6 pt-6 border-t border-white/10">
                <p class="text-center text-white/60 text-sm">Or</p>
                <a href="{{ route('register') }}" class="block mt-3 text-center text-violet-400 hover:text-violet-300">
                    Sign up with email
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
