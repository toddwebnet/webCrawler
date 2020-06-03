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
        $this->line("pop");
        $numPops = env('NUM_POPS', 1);
        if (!is_numeric($numPops)) {
            $numPops = 1;
        }

        for ($x = 0; $x < $numPops; $x++) {
            app()->make(UrlProvider::class)->popToQueue();
            sleep(5);
        }
    }
}
