<?php

namespace App\Console\Commands;

use App\Services\Queues\QueueHtmlService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessQueueHtmls extends Command
{
    protected $signature = 'processQueueHtmls {overrideId?}';
    protected $description = "Process Queue Htmls";

    public function handle()
    {
//        Log::info('Job Run: processQueueHtmls');
        $overrideId = $this->argument('overrideId');
        $this->line(app()->make(
            QueueHtmlService::class, ['overrideId' => $overrideId])->process()
        );
    }
}
