<?php

namespace App\Console\Commands;


use App\Jobs\TestLogJob;
use App\Models\Url;
use App\Models\UrlSizes;
use App\Services\HtmlParserService;
use App\Services\Providers\HtmlProvider;
use App\Services\Queues\QueueHtmlService;
use App\Services\Queues\QueueUrlService;
use App\Services\QueueService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class Test extends Command
{
    protected $signature = 'test';

    public function handle()
    {


        dump(
            UrlSizes::getTodayCount()
        );
        dump(
            UrlSizes::allowDownloads()
        );

    }

    public function handle2()
    {
        $url = Url::find(2);

        try {
            app()->make(HtmlProvider::class)->addToQueue(
                $url->id,
                app()->make(HtmlParserService::class)->getUrl($url->url)
            );
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $url->is_valid = false;
            $url->save();
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }
}
