<?php

namespace App\Services;

use App\Jobs\SendPushNotificationJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RetentionService
{
    private CouponRankingService $rankingService;

    public function __construct(CouponRankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function sendDailyDealsToActiveUsers(): void
    {
        $users = User::where('role', 'user')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereHas('pushSubscriptions')
            ->get();

        foreach ($users as $user) {
            $prefs = $user->notificationPreference;
            if ($prefs && !$prefs->daily_deals) continue;
            if ($prefs && $prefs->isInSilentHours()) continue;
            try {
                $coupons = $this->rankingService->getRankedFeed(
                    $user->lat,
                    $user->lng,
                    $user,
                    null,
                    3
                );
                if ($coupons->isNotEmpty()) {
                    $title = '🔥 Top deals near you!';
                    $body = $coupons->first()->title . ' — and ' . ($coupons->count() - 1) . ' more';
                    SendPushNotificationJob::dispatch($user, $title, $body);
                }
            } catch (\Throwable $e) {
                Log::warning('Daily deals push failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }
    }

    public function notifyInactiveUsersComeback(): void
    {
        $cutoff = now()->subDays(7);
        $users = User::where('role', 'user')
            ->where(function ($q) use ($cutoff) {
                $q->where('last_active_at', '<', $cutoff)
                    ->orWhereNull('last_active_at');
            })
            ->whereHas('pushSubscriptions')
            ->where('redemption_count', '>', 0)
            ->limit(500)
            ->get();

        foreach ($users as $user) {
            $prefs = $user->notificationPreference;
            if ($prefs && !$prefs->comeback) continue;
            if ($prefs && $prefs->isInSilentHours()) continue;
            SendPushNotificationJob::dispatch(
                $user,
                'We miss you! 💫',
                'Come back and claim exclusive comeback deals near you.'
            );
        }
    }

    public function checkSavingsMilestones(User $user): ?array
    {
        $milestones = [100, 500, 1000, 5000];
        $saved = (float) $user->total_saved;
        $notified = $user->milestones_notified ?? [];
        if (!is_array($notified)) {
            $notified = [];
        }

        foreach ($milestones as $m) {
            if ($saved >= $m && !in_array($m, $notified)) {
                $notified[] = $m;
                $user->update(['milestones_notified' => $notified]);
                return ['amount' => $m, 'total' => $saved];
            }
        }
        return null;
    }

    public function updateRedemptionStreak(User $user): void
    {
        $redemptions = $user->redemptions()->orderByDesc('created_at')->get();
        if ($redemptions->isEmpty()) {
            return;
        }

        $weeksWithRedemption = $redemptions->map(fn ($r) => $r->created_at->format('o-W'))->unique()->sortDesc()->values()->all();

        $streak = 0;
        $checkWeek = now()->format('o-W');
        foreach ($weeksWithRedemption as $weekStr) {
            if ($weekStr !== $checkWeek) {
                break;
            }
            $streak++;
            $checkWeek = now()->subWeeks($streak)->format('o-W');
        }

        $updates = ['redemption_streak_weeks' => $streak];
        if ($streak >= 3) {
            $updates['vip_unlocked'] = true;
        }
        $user->update($updates);
    }
}
