<?php

namespace App\Providers;

use App\Models\Coupon;
use App\Policies\CouponPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Coupon::class => CouponPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
