<?php

namespace App\Jobs;

use App\Services\Queues\QueueHtmlService;

class HtmlJob extends BaseJob
{
    private $htmlId;

    public function __construct($args)
    {
        $requiredKeys = ['htmlId'];
        $this->checkKeys($args, $requiredKeys);
        $this->htmlId = $args['htmlId'];
    }

    public function handle()
    {
        app()->make(QueueHtmlService::class)->process($this->htmlId);
    }

}
