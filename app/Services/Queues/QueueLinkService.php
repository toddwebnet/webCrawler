<?php

namespace App\Services\Queues;

use App\Models\Link;
use App\Models\QueueLink;
use App\Services\Providers\UrlProvider;
use Illuminate\Support\Facades\Log;

class QueueLinkService
{

    public function process($linkId)
    {
        $link = Link::find($linkId);
        if ($link === null) {
            Log::warning(get_class($this) . "->process({$linkId}) - Link Not Found");
            return;
        }
        Log::info('Adding to Url Queue: ' . $link->link);
        app()->make(UrlProvider::class)->addNewUrl($link->link);
        return 1;
    }

}
