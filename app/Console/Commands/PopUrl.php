<?php

namespace App\Console\Commands;

use App\Services\Providers\UrlProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PopUrl extends Command
{
    protected $signature = 'pop-url';

    public function handle()
    {
        $this->line("pop-url");
        Log::info('pop-url');
        app()->make(UrlProvider::class)->popToQueue();
    }
}
