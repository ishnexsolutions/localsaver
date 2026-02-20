<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;

class ExpireCouponsCommand extends Command
{
    protected $signature = 'coupons:expire';
    protected $description = 'Mark expired coupons as expired';

    public function handle(): int
    {
        $count = Coupon::where('status', 'active')
            ->where('expiry_date', '<', now()->toDateString())
            ->update(['status' => 'expired']);

        $this->info("Expired {$count} coupons.");
        return Command::SUCCESS;
    }
}
