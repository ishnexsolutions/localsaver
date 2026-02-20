<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load(['redemptions.coupon.business', 'notificationPreference']);
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        auth()->user()->update($request->only('name', 'email'));
        return back()->with('success', 'Profile updated.');
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'daily_deals' => 'boolean',
            'flash_deals' => 'boolean',
            'milestones' => 'boolean',
            'comeback' => 'boolean',
            'silent_start' => 'nullable|date_format:H:i',
            'silent_end' => 'nullable|date_format:H:i',
        ]);

        \App\Models\NotificationPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'daily_deals' => $request->boolean('daily_deals'),
                'flash_deals' => $request->boolean('flash_deals'),
                'milestones' => $request->boolean('milestones'),
                'comeback' => $request->boolean('comeback'),
                'silent_start' => $request->silent_start ?: null,
                'silent_end' => $request->silent_end ?: null,
            ]
        );
        return back()->with('success', 'Notification preferences updated.');
    }
}
