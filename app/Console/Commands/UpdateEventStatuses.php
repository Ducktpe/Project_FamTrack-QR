<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DistributionEvent;
use Carbon\Carbon;

class UpdateEventStatuses extends Command
{
    protected $signature   = 'events:update-statuses';
    protected $description = 'Auto-update distribution event statuses based on started_at and ended_at';

    public function handle()
    {
        $now = Carbon::now();

        // upcoming → ongoing when started_at is reached
        $started = DistributionEvent::where('status', 'upcoming')
            ->whereNotNull('started_at')
            ->where('started_at', '<=', $now)
            ->update(['status' => 'ongoing']);

        // ongoing → completed when ended_at is reached
        $ended = DistributionEvent::where('status', 'ongoing')
            ->whereNotNull('ended_at')
            ->where('ended_at', '<=', $now)
            ->update(['status' => 'completed']);

        $this->info("Updated: {$started} started, {$ended} completed.");
    }
}