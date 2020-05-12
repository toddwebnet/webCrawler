<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class QueueJob extends Command
{
    protected $signature = 'queueJob {jobName}
                               {--sleep=}
                               {--once}
                           ';
    protected $description = 'Custom Queue Job Runner Service';

    public function handle()
    {
        $jobName = $this->argument('jobName');
        $sleep = $this->option('sleep') == null ? 3 : $this->option('sleep');
        $once = $this->option('once');
        Log::info('staring queue job for: ' . $jobName);
        while (true) {
            Artisan::call($jobName);
            $op = Artisan::output();
            if ($once) {
                break;
            }

            if (!(bool)trim($op)) {
                sleep($sleep);
            }
        }
    }
}
