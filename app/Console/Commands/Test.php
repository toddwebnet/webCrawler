<?php

namespace App\Console\Commands;


use App\Jobs\TestLogJob;
use App\Models\Url;
use App\Services\HtmlParserService;
use App\Services\Providers\HtmlProvider;
use App\Services\QueueService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class Test extends Command
{
    protected $signature = 'test';

    public function handle()
    {
        app()->make(QueueService::class)->sendToQueue(TestLogJob::class, [
            'logStatement' => 'QueueTest',
            'test'
        ]);

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
