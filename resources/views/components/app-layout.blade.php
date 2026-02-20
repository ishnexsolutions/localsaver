<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f0a1e">
    <meta name="description" content="LocalSaver - Discover and redeem nearby coupons. Save money on local deals.">
    <link rel="manifest" href="/manifest.json">
    <title>{{ $title ?? 'LocalSaver' }} - Deals Near You</title>
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css">
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    @endif
    @stack('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-indigo-950/50 to-slate-950 text-white">
    <nav class="sticky top-0 z-40 bg-white/5 backdrop-blur-xl border-b border-white/10">
        <div class="max-w-6xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xl font-bold bg-gradient-to-r from-violet-400 to-purple-500 bg-clip-text text-transparent">
                    LocalSaver
                </a>
                <a href="{{ route('businesses.index') }}" class="text-sm text-white/80 hover:text-white transition">Discover Businesses</a>
                <div class="flex items-center gap-3">
                    @auth
                        @if(auth()->user()->role === 'user')
                            <a href="{{ route('profile') }}" class="text-sm text-white/80 hover:text-white transition">Profile</a>
                        @elseif(auth()->user()->role === 'business')
                            <a href="{{ route('business.dashboard') }}" class="text-sm text-white/80 hover:text-white transition">Dashboard</a>
                            <a href="{{ route('business.partnerships.index') }}" class="text-sm text-white/80 hover:text-white transition">Partners</a>
                        @elseif(auth()->user()->role === 'admin')
                            <a href="{{ route('founder.dashboard') }}" class="text-sm text-white/80 hover:text-white transition">Founder</a>
                            <a href="{{ route('admin.dashboard') }}" class="text-sm text-white/80 hover:text-white transition">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-white/60 hover:text-white transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-white/80 hover:text-white transition">Login</a>
                        <a href="{{ route('register') }}" class="btn-glow text-sm">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-6 pb-24">
        @if(session('success'))
            <div class="mb-4 p-4 glass-card rounded-xl text-green-300 border-green-400/30">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 glass-card rounded-xl text-red-300 border-red-400/30">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-4 glass-card rounded-xl text-red-300 border-red-400/30">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="border-t border-white/10 py-6 mt-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-wrap gap-4 justify-center text-sm text-white/50">
                <a href="{{ route('legal.terms') }}" class="hover:text-white">Terms</a>
                <a href="{{ route('legal.privacy') }}" class="hover:text-white">Privacy</a>
                <a href="{{ route('legal.coupon-rules') }}" class="hover:text-white">Coupon Rules</a>
                <a href="{{ route('legal.refund') }}" class="hover:text-white">Refund Policy</a>
                <a href="{{ route('legal.authenticity') }}" class="hover:text-white">Business Authenticity</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
    </script>
</body>
</html>
