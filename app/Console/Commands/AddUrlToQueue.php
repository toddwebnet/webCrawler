<?php

namespace App\Console\Commands;

use App\Services\Providers\UrlProvider;
use Illuminate\Console\Command;

class AddUrlToQueue extends Command
{
    protected $signature = 'addUrlToQueue {url}';

    protected $description = 'Adds URL to the queue to process';

    public function handle()
    {
        $url = $this->argument('url');
        $urlObj = app()->make(UrlProvider::class)->addNewUrl($url);

        $this->line('done');


    }
}
