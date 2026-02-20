<?php

namespace App\Console\Commands;

use App\Services\BoostService;
use Illuminate\Console\Command;

class ExpireBoostedCommand extends Command
{
    protected $signature = 'boost:expire';
    protected $description = 'Expire boosted coupons past their boost_until date';

    public function handle(BoostService $boost): int
    {
        $count = $boost->expireBoostedCoupons();
        $this->info("Expired {$count} boosted coupons.");
        return Command::SUCCESS;
    }
}
