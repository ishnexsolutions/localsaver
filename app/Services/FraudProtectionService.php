<?php

namespace App\Services;

use App\Models\FraudFlag;
use App\Models\Redemption;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FraudProtectionService
{
    private const REDEMPTION_COOLDOWN_MINUTES = 60;
    private const MAX_REDEMPTIONS_PER_DAY = 10;
    private const RAPID_REDEMPTION_THRESHOLD = 5;
    private const RAPID_REDEMPTION_WINDOW_MINUTES = 10;

    public function canRedeem(User $user, int $couponId, ?string $ipHash = null, ?string $deviceHash = null): array
    {
        if ($user->suspended ?? false) {
            return [false, 'Your account has been suspended. Please contact support.'];
        }

        $pendingFlags = FraudFlag::where('user_id', $user->id)->where('status', 'pending')->exists();
        if ($pendingFlags) {
            return [false, 'Your account is under review. Please try again later.'];
        }

        $lastRedemption = Redemption::where('user_id', $user->id)
            ->where('coupon_id', $couponId)
            ->latest()
            ->first();
        if ($lastRedemption && $lastRedemption->created_at->diffInMinutes(now()) < self::REDEMPTION_COOLDOWN_MINUTES) {
            $this->flagFraud($user->id, 'cooldown_violation', $ipHash, $deviceHash, [
                'coupon_id' => $couponId,
                'last_redemption_at' => $lastRedemption->created_at->toIso8601String(),
            ]);
            return [false, 'Please wait before redeeming this coupon again.'];
        }

        $todayCount = Redemption::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();
        if ($todayCount >= self::MAX_REDEMPTIONS_PER_DAY) {
            $this->flagFraud($user->id, 'daily_limit', $ipHash, $deviceHash, [
                'count' => $todayCount,
                'limit' => self::MAX_REDEMPTIONS_PER_DAY,
            ]);
            return [false, 'Daily redemption limit reached.'];
        }

        $recentCount = Redemption::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes(self::RAPID_REDEMPTION_WINDOW_MINUTES))
            ->count();
        if ($recentCount >= self::RAPID_REDEMPTION_THRESHOLD) {
            $this->flagFraud($user->id, 'rapid_redemptions', $ipHash, $deviceHash, [
                'count' => $recentCount,
                'window_minutes' => self::RAPID_REDEMPTION_WINDOW_MINUTES,
            ]);
            return [false, 'Suspicious activity detected. Please try again later.'];
        }

        return [true, ''];
    }

    public function flagFraud(
        ?int $userId,
        string $reason,
        ?string $ipHash = null,
        ?string $deviceHash = null,
        array $metadata = []
    ): FraudFlag {
        return FraudFlag::create([
            'user_id' => $userId,
            'reason' => $reason,
            'ip_hash' => $ipHash,
            'device_hash' => $deviceHash,
            'metadata' => $metadata,
        ]);
    }

    public function logSuspiciousRedemption(Redemption $redemption, string $reason, ?string $ipHash = null): void
    {
        $this->flagFraud(
            $redemption->user_id,
            $reason,
            $ipHash,
            null,
            ['redemption_id' => $redemption->id]
        );
    }

    public static function hashIp(string $ip): string
    {
        return hash('sha256', $ip . config('app.key'));
    }

    public static function hashDevice(string $userAgent): string
    {
        return hash('sha256', $userAgent . config('app.key'));
    }
}
