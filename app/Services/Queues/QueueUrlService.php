<?php

namespace App\Services\Queues;

use App\Jobs\UrlJob;
use App\Models\QueueUrl;
use App\Models\Url;
use App\Models\UrlOverflow;
use App\Models\UrlSizes;
use App\Services\HtmlParserService;
use App\Services\Providers\HtmlProvider;
use App\Services\QueueService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class QueueUrlService
{

    public function process($urlId)
    {
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

    public function addUrlObjToQueue(Url $url)
    {
        Log::info("adding URL to queue: {$url->url}");
        app()->make(QueueService::class)->sendToQueue(UrlJob::class, [
            'urlId' => $url->id
        ], 'urls');
        $url->last_refreshed = new Carbon();
        $url->save();
    }

    public function reloadFirstOverflow()
    {
        try {
            $urlOverflow = UrlOverflow::first();
        } catch (ModelNotFoundException $e) {
            return;
        }

        if ($urlOverflow === null) {
            return;
        }

        try {
            $url = Url::find($urlOverflow->url_id);
            if ($url !== null) {
                $this->addUrlObjToQueue($url);
            }
        } catch (\Exception $e) {
            // do nothing
        }
        $urlOverflow->delete();
    }

}
