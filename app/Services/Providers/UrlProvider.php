<?php

namespace App\Services\Providers;

use App\Jobs\UrlJob;
use App\Models\QueueUrl;
use App\Models\Url;
use App\Models\UrlOverflow;
use App\Models\UrlSizes;
use App\Services\Queues\QueueUrlService;
use App\Services\QueueService;
use App\Services\UrlParserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UrlProvider
{
    /**
     * @param $url
     */
    public function addNewUrl($url)
    {
        $urlObj = $this->getObj($url);
        if ($urlObj->last_refreshed === null) {
            UrlOverflow::create(['url_id' => $urlObj->id]);
        }
        return $urlObj;

    }

    /**
     * @param $url
     * @return Url
     */
    private function getObj($url)
    {
        $urlObj = Url::findUrl($url);
        if ($urlObj === null) {
            $host = (app()->make(UrlParserService::class, ['url' => $url])->parse())['host'];
            $urlObj = Url::updateOrCreate(['url' => $url, 'host' => $host]);
        } else {
            Log::info('------------------------ STOPPED: ' . $url);
        }
        return $urlObj;
    }

    public function addUrlObjToQueue(Url $url)
    {

        app()->make(QueueService::class)->sendToQueue(UrlJob::class, [
            'urlId' => $url->id
        ], 'urls');
        $url->last_refreshed = new Carbon();
        $url->save();
    }

    public function popToQueue()
    {
        if (!UrlSizes::allowDownloads()) {
            return;
        }
        $overFlow = UrlOverflow::first();
        if ($overFlow === null) {
            return;
        }
        $url = Url::find($overFlow->url_id);
        if ($url !== null) {
            $this->addUrlObjToQueue($url);
        }
        $overFlow->delete();
    }
}
