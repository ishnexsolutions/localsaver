<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'business@localsaver.com'],
            ['name' => 'Sample Business', 'password' => bcrypt('password'), 'role' => 'business']
        );

        $business = $user->business()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Tasty Bites Restaurant',
                'category' => 'food',
                'address' => '123 Main St, City',
                'lat' => 28.6139,
                'lng' => 77.2090,
                'radius_km' => 5,
                'verified' => true,
                'activated' => true,
                'activated_at' => now(),
            ]
        );

        $business->coupons()->firstOrCreate(
            ['title' => '20% Off Your Order'],
            [
                'description' => 'Get 20% off on orders above ₹500. Valid for dine-in and takeaway.',
                'discount_value' => 20,
                'discount_type' => 'percentage',
                'max_redemptions' => 100,
                'expiry_date' => now()->addMonth(),
                'radius_km' => 5,
            ]
        );
    }
}
