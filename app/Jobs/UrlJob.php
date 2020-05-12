<?php

namespace App\Jobs;

use App\Services\Queues\QueueUrlService;
use Illuminate\Support\Facades\Log;

class UrlJob extends BaseJob
{
    private $urlId;

    public function __construct($args)
    {
        $requiredKeys = ['urlId'];
        $this->checkKeys($args, $requiredKeys);
        $this->urlId = $args['urlId'];
    }

    public function handle()
    {
        app()->make(QueueUrlService::class)->process($this->urlId);
    }

}
