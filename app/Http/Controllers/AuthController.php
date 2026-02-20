<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(
        private OtpService $otpService,
        private ReferralService $referralService
    ) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required|digits:10']);

        $otp = $this->otpService->generate($request->phone);
        $this->otpService->sendOtp($otp);

        return back()->with('otp_sent', true)->with('phone', $request->phone);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'code' => 'required|digits:6',
        ]);

        if (!$this->otpService->verify($request->phone, $request->code)) {
            return back()->withErrors(['code' => 'Invalid or expired OTP.'])->withInput();
        }

        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            ['name' => 'User ' . substr($request->phone, -4), 'role' => 'user']
        );

        $refCode = $request->query('ref') ?? session('referral_code');
        if ($refCode && !$user->referred_by) {
            $this->referralService->registerReferral($user, $refCode);
        }

        Auth::login($user);

        if (!$user->onboarding_completed) {
            return redirect()->route('onboarding.user');
        }
        return redirect()->intended(route('home'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|digits:10',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => 'user',
        ]);

        $refCode = $request->input('referral_code') ?? $request->query('ref');
        if ($refCode) {
            $this->referralService->registerReferral($user, $refCode);
        }

        Auth::login($user);
        if ($user->role === 'user' && !$user->onboarding_completed) {
            return redirect()->route('onboarding.user');
        }
        return redirect()->route('home');
    }

    public function showBusinessRegister()
    {
        return view('auth.business-register');
    }

    public function registerBusiness(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'business_name' => 'required|string|max:255',
            'category' => 'required|in:food,salon,health,services,shopping',
            'address' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'business',
        ]);

        $user->business()->create([
            'name' => $request->business_name,
            'category' => $request->category,
            'address' => $request->address,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        Auth::login($user);
        return redirect()->route('business.payment');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
