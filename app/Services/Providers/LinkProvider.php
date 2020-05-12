<?php

namespace App\Services\Providers;

use App\Jobs\LinkJob;
use App\Models\Link;
use App\Services\QueueService;

class LinkProvider
{

    public function addToQueue($urlId, $link, $text)
    {
        app()->make(QueueService::class)->sendToQueue(LinkJob::class, [
            'linkId' => $this->getObj($urlId, $link, $text)->id
        ], 'links');

    }

    private function getObj($urlId, $link, $text)
    {
        return Link::create([
            'url_id' => $urlId,
            'link' => $link,
            'text' => $text
        ]);

    }
}
