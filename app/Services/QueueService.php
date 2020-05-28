<?php

namespace App\Services;

use App\Helpers\QueueHelpers;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\DispatchesJobs;

class QueueService
{

    use DispatchesJobs, Queueable;

    const DEFAULT_QUEUE = 'tests';

    const AVAILABLE_QUEUES = [
        'tests',
        'htmls',
        'urls',
        'links'
    ];

    public function sendToQueue($class, $args = null, $queue = null, $runNow = false)
    {
        if ($queue == null) {
            $queue = self::DEFAULT_QUEUE;
        }

        $job = new $class($args);

        if ($runNow === true) {
            $this->dispatchNow($job->onQueue($queue));
        } else {
            $this->dispatch($job->onQueue($queue));
        }
    }

}
