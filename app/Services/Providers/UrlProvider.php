<?php

namespace App\Services\Providers;

use App\Jobs\UrlJob;
use App\Models\QueueUrl;
use App\Models\Url;
use App\Services\QueueService;
use App\Services\UrlParserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UrlProvider
{
    /**
     * @param $url
     */
    public function addNewUrl($url)
    {
        if (Cache::get($url) === null) {
            Cache::put($url, 1, 60);
            $urlObj = $this->getObj($url);
            if ($urlObj->last_refreshed === null) {
                $this->addUrlObjToQueue($urlObj);
            }
        }
    }

    public function addUrlObjToQueue(Url $url)
    {

        app()->make(QueueService::class)->sendToQueue(UrlJob::class, [
            'urlId' => $url->id
        ], 'urls');
        $url->last_refreshed = new Carbon();
        $url->save();
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
}
