<?php

namespace App\Console\Commands;

use App\Helpers\Utils;
use App\Jobs\TestLogJob;
use App\Models\Url;
use App\Models\UrlSize;
use App\Services\HtmlParserService;
use App\Services\Providers\HtmlProvider;
use App\Services\Queues\QueueHtmlService;
use App\Services\Queues\QueueUrlService;
use App\Services\QueueService;
use App\Services\UrlParserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class Test extends Command
{
    protected $signature = 'test';

    public function handle()
    {

        $limit = UrlSize::getDailyLimit();
        $todayCount = UrlSize::getTodaySum();
        $allowDownloads = UrlSize::allowDownloads();

        $op = [
            'count' => UrlSize::getTodayCount(),
            'limit' => $this->formatBytes($limit),
            'today' => $this->formatBytes($todayCount),
            'remaining' => $this->formatBytes($limit-$todayCount),
            'allow' => $allowDownloads ? 'yes' : 'no'
        ];
        dump(
            $op
        );

    }

    public function handle3()
    {

        //app()->make(QueueUrlService::class)->process(1);

        app()->make(QueueHtmlService::class)->process(1);



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

    function formatBytes($bytes)
    {
        $bytes = (int)round($bytes, 0);

        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $precision = 0;
        $digits = strlen((string)$bytes);
        for ($x = 0; $x < 4; $x++) {
            // 4, (4+3), (4+3+3), (4+3+3+3)
            if ($digits > (4 + (3 * $x))) {
                $bytes = $bytes/1024;
                $precision++;
            }
        }


        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$precision];
    }
}
