<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('coupons:expire')->daily();
Schedule::command('boost:expire')->hourly();
Schedule::command('retention:daily')->dailyAt('10:00');
Schedule::command('analytics:snapshot')->dailyAt('23:55');
Schedule::command('businesses:update-popularity')->daily();
