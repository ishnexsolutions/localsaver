<x-app-layout title="My Profile">
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="glass-card p-6">
            <h2 class="text-xl font-bold mb-4">Your Wallet</h2>
            <div class="text-4xl font-bold text-emerald-400 mb-1">₹{{ number_format($user->total_saved, 0) }}</div>
            <p class="text-white/60">Total saved from {{ $user->redemption_count }} redemptions</p>
            <div class="flex flex-wrap gap-2 mt-2">
                @if($user->isVip())
                    <span class="inline-block px-3 py-1 bg-amber-500/30 text-amber-200 rounded-lg text-sm">⭐ VIP Member</span>
                @endif
                @if($user->founding_member ?? false)
                    <span class="inline-block px-3 py-1 bg-violet-500/30 text-violet-200 rounded-lg text-sm">🏅 Founding Member</span>
                @endif
                @if(($user->redemption_streak_weeks ?? 0) >= 1)
                    <span class="inline-block px-3 py-1 bg-emerald-500/30 text-emerald-200 rounded-lg text-sm">🔥 {{ $user->redemption_streak_weeks }} week streak</span>
                @endif
                @if(\App\Services\GrowthHookService::hasRedeem5Reward($user))
                    <span class="inline-block px-3 py-1 bg-amber-500/30 text-amber-200 rounded-lg text-sm">🎁 Redeem 5 unlocked</span>
                @endif
            </div>
        </div>

        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Referral Code</h3>
            <p class="text-white/60 text-sm mb-2">Share your code for rewards</p>
            <div class="flex items-center gap-3">
                <code class="px-4 py-2 bg-white/10 rounded-lg font-mono text-lg">{{ $user->referral_code }}</code>
                <button onclick="navigator.clipboard.writeText('{{ $user->referral_code }}')" class="text-sm text-violet-400">Copy</button>
            </div>
        </div>

        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Notification Preferences</h3>
            @php $prefs = $user->notificationPreference; @endphp
            <form method="POST" action="{{ route('profile.notifications') }}" class="space-y-4 mb-6">
                @csrf
                @method('PUT')
                <div class="space-y-3">
                    <label class="flex items-center gap-2"><input type="checkbox" name="daily_deals" value="1" {{ ($prefs->daily_deals ?? true) ? 'checked' : '' }}> Daily deals</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="flash_deals" value="1" {{ ($prefs->flash_deals ?? true) ? 'checked' : '' }}> Flash deals</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="milestones" value="1" {{ ($prefs->milestones ?? true) ? 'checked' : '' }}> Milestones</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="comeback" value="1" {{ ($prefs->comeback ?? true) ? 'checked' : '' }}> Comeback reminders</label>
                </div>
                <div class="flex gap-4 items-center">
                    <div>
                        <label class="block text-sm text-white/60 mb-1">Silent hours start</label>
                        <input type="time" name="silent_start" value="{{ ($start = $prefs->silent_start ?? null) ? (is_object($start) ? $start->format('H:i') : substr((string)$start, 0, 5)) : '22:00' }}" class="px-3 py-2 rounded-lg bg-white/10 border border-white/20">
                    </div>
                    <div>
                        <label class="block text-sm text-white/60 mb-1">Silent hours end</label>
                        <input type="time" name="silent_end" value="{{ ($end = $prefs->silent_end ?? null) ? (is_object($end) ? $end->format('H:i') : substr((string)$end, 0, 5)) : '08:00' }}" class="px-3 py-2 rounded-lg bg-white/10 border border-white/20">
                    </div>
                </div>
                <button type="submit" class="btn-glow">Save preferences</button>
            </form>
        </div>

        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Profile</h3>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm text-white/60 mb-2">Name</label>
                    <input type="text" name="name" value="{{ $user->name }}" required
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-2">Email (optional)</label>
                    <input type="email" name="email" value="{{ $user->email }}"
                        class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 focus:border-violet-400/50 outline-none transition">
                </div>
                <button type="submit" class="btn-glow">Save</button>
            </form>
        </div>

        <div class="glass-card p-6">
            <h3 class="font-semibold mb-4">Recent Redemptions</h3>
            @forelse($user->redemptions->take(5) as $redemption)
                <div class="flex justify-between py-3 border-b border-white/10 last:border-0">
                    <div>
                        <p class="font-medium">{{ $redemption->coupon->title }}</p>
                        <p class="text-sm text-white/50">{{ $redemption->coupon->business->name }}</p>
                    </div>
                    <span class="text-emerald-400">+₹{{ $redemption->savings_amount }}</span>
                </div>
            @empty
                <p class="text-white/50">No redemptions yet</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
