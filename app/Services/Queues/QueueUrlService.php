<?php

namespace App\Services\Queues;

use App\Models\QueueUrl;
use App\Models\Url;
use App\Models\UrlSizes;
use App\Services\HtmlParserService;
use App\Services\Providers\HtmlProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class QueueUrlService
{

    public function process($urlId)
    {
        if (!UrlSizes::allowDownloads()) {

            return;
        }
        $url = Url::find($urlId);

        if ($url === null) {
            Log::warning(get_class($this) . "->process({$urlId}) - URL Not Found");
            return;
        }
        try {
            $html = app()->make(HtmlParserService::class)
                ->getS3Url($url, ['validate', 'log_sizes']);
        } catch (\Exception $e) {
            Log::warning(get_class($this) . "->process({$urlId}) - Invalid URL: {$url->url}");
            $url->is_valid = false;
            $url->save();
            return;
        }

        app()->make(HtmlProvider::class)->addToQueue(
            $url->id,
            $html
        );
        $url->last_refreshed = new Carbon();
        $url->save();
        return;
    }
}
