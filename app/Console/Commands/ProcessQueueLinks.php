<?php

namespace App\Console\Commands;

use App\Services\Queues\QueueLinkService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessQueueLinks extends Command
{
    protected $signature = 'processQueueLinks {overrideId?}';
    protected $description = "Process Queue Links";

    public function handle()
    {
//        Log::info('Job Run: processQueueLinks');
        $overrideId = $this->argument('overrideId');
        app()->make(QueueLinkService::class, ['overrideId' => $overrideId])->process();
        $this->line('done');
    }
}
