<?php

namespace App\Helpers;

use App\Http\Middleware\ConnectDatabase;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class QueueHelpers
 * @package App\Helpers
 *
 * to run queues
 *
 * php artisan queue:work --queue=faculty180
 *
 *
 */
class QueueHelpers
{
    use DispatchesJobs, Queueable;

    const DEFAULT_QUEUE = 'tests';

    const AVAILABLE_QUEUES = [
        'tests',
        'htmls',
        'urls',
        'links'
    ];

    public static function sendToQueue($class, $args = null, $queue = null, $runNow = false)
    {
        if ($queue == null) {
            $queue = self::DEFAULT_QUEUE;
        }

        $job = new $class($args);
        $self = app()->make(self::class);
        if ($runNow === true) {
            $self->dispatchNow($job->onQueue($queue));
        } else {
            $self->dispatch($job->onQueue($queue));
        }
    }

}
