<?php

namespace App\Console\Commands;

use App\Services\RetentionService;
use Illuminate\Console\Command;

class DailyRetentionCommand extends Command
{
    protected $signature = 'retention:daily';
    protected $description = 'Send daily deals and inactive user notifications';

    public function handle(RetentionService $retention): int
    {
        $retention->sendDailyDealsToActiveUsers();
        $this->info('Daily deals sent.');
        $retention->notifyInactiveUsersComeback();
        $this->info('Inactive notifications sent.');
        return Command::SUCCESS;
    }
}
