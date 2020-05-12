<?php

namespace App\Console\Commands;

use App\Services\Queues\QueueUrlService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessQueueUrls extends Command
{
    protected $signature = 'processQueueUrls {overrideId?}';
    protected $description = "Process Queue Urls";

    public function handle()
    {
//        Log::info('Job Run: processQueueUrls');
        $overrideId = $this->argument('overrideId');
        $this->line(
            app()->make(QueueUrlService::class, ['overrideId' => $overrideId])->process()
        );

    }
}
