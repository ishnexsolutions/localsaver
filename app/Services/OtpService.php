<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Str;

class OtpService
{
    public function generate(string $phone): OtpCode
    {
        OtpCode::where('phone', $phone)->delete();

        return OtpCode::create([
            'phone' => $phone,
            'code' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    public function verify(string $phone, string $code): bool
    {
        $otp = OtpCode::where('phone', $phone)
            ->where('code', $code)
            ->where('verified', false)
            ->first();

        if (!$otp || $otp->isExpired()) {
            return false;
        }

        $otp->update(['verified' => true]);
        return true;
    }

    public function sendOtp(OtpCode $otp): void
    {
        // In production: integrate with SMS gateway (Twilio, MSG91, etc.)
        // For now: log it for development
        if (app()->environment('local')) {
            \Log::info("OTP for {$otp->phone}: {$otp->code}");
        }
    }
}
