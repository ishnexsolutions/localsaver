<?php

namespace App\Console\Commands;

use App\Services\AnalyticsTrackingService;
use Illuminate\Console\Command;

class AnalyticsSnapshotCommand extends Command
{
    protected $signature = 'analytics:snapshot';
    protected $description = 'Record daily analytics snapshot';

    public function handle(AnalyticsTrackingService $analytics): int
    {
        $analytics->recordDailySnapshot();
        $this->info('Analytics snapshot recorded.');
        return Command::SUCCESS;
    }
}
