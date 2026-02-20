<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f0a1e">
    <meta name="description" content="LocalSaver - Discover and redeem nearby coupons. Save money on local deals.">
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
    {{-- PWA Install Banner (Alpine) --}}
    <div x-data="{ show: false }" x-show="show" x-init="
        if ('standalone' in window.navigator && !window.navigator.standalone) {
            const installed = localStorage.getItem('pwa-installed');
            if (!installed) show = true;
        }
    " class="fixed bottom-0 left-0 right-0 z-50 p-4 bg-black/50 backdrop-blur-xl border-t border-white/10" style="display: none;">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <span class="text-sm">Install LocalSaver for quick access</span>
            <div class="flex gap-2">
                <button @click="localStorage.setItem('pwa-installed', '1'); show = false" class="text-sm text-gray-400">Later</button>
                <button onclick="document.getElementById('install-pwa')?.click()" class="btn-glow text-sm py-1.5 px-3">Install</button>
            </div>
        </div>
    </div>

    <nav class="sticky top-0 z-40 bg-white/5 backdrop-blur-xl border-b border-white/10">
        <div class="max-w-6xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xl font-bold bg-gradient-to-r from-violet-400 to-purple-500 bg-clip-text text-transparent">
                    LocalSaver
                </a>
                <div class="flex items-center gap-3">
                    @auth
                        @if(auth()->user()->role === 'user')
                            <a href="{{ route('profile') }}" class="text-sm text-white/80 hover:text-white transition">Profile</a>
                        @elseif(auth()->user()->role === 'business')
                            <a href="{{ route('business.dashboard') }}" class="text-sm text-white/80 hover:text-white transition">Dashboard</a>
                        @elseif(auth()->user()->role === 'admin')
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

    @stack('scripts')
</body>
</html>
