<div {{ $attributes->merge(['class' => 'bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-lg p-6 transition-all duration-300 hover:bg-white/15 hover:shadow-glow hover:border-violet-400/20']) }}>
    {{ $slot }}
</div>
