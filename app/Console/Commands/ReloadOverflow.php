<?php
namespace App\Console\Commands;

use App\Models\UrlSize;
use App\Services\Queues\QueueUrlService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReloadOverflow extends Command
{
    protected $signature = 'reloadOverflow';

    protected $description = 'reload Overflow';

    public function handle()
    {
        Log::info("running reload overflow");
        if (!UrlSize::allowDownloads()) {
            $this->warn('No more downloads for today');
            return;
        }
        app()->make(QueueUrlService::class)->reloadFirstOverflow();
    }

}
