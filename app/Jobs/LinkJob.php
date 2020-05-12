<?php

namespace App\Jobs;

use App\Services\Queues\QueueLinkService;

class LinkJob extends BaseJob
{
    private $linkId;

    public function __construct($args)
    {
        $requiredKeys = ['linkId'];
        $this->checkKeys($args, $requiredKeys);
        $this->linkId = $args['linkId'];
    }

    public function handle()
    {
        app()->make(QueueLinkService::class)->process($this->linkId);
    }
}
