<?php

namespace App\Console\Commands;

use App\Services\BusinessDiscoveryService;
use Illuminate\Console\Command;

class UpdateBusinessPopularityCommand extends Command
{
    protected $signature = 'businesses:update-popularity';
    protected $description = 'Update popularity scores for all businesses';

    public function handle(BusinessDiscoveryService $discovery): int
    {
        $businesses = \App\Models\Business::all();
        foreach ($businesses as $business) {
            $discovery->updatePopularityScore($business);
        }
        $this->info("Updated {$businesses->count()} businesses.");
        return Command::SUCCESS;
    }
}
