<?php

namespace App\Services;


use App\Helpers\QueueHelpers;

class QueueService
{
    public function sendToQueue($class, $args = null, $queue = null, $runNow = false)
    {
        QueueHelpers::sendToQueue($class, $args, $queue, $runNow);
    }
}
