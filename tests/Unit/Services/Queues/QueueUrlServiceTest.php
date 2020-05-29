<?php

namespace Tests\Unit\Services\Queues;

use App\Models\Url;
use App\Models\UrlOverflow;
use App\Services\HtmlParserService;
use App\Services\Providers\HtmlProvider;
use App\Services\Queues\QueueUrlService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueueUrlServiceTest extends TestCase
{
    public function testProcess()
    {
        $urlObj = Url::create([
            'host' => 'gomer.com',
            'url' => 'http://gomer.com'
        ]);

        $html = "<html></html>";

        $this->setMock(HtmlParserService::class)
            ->shouldReceive('getS3Url')
            ->withAnyArgs()
            ->once()
            ->andReturn($html);

        $this->setMock(HtmlProvider::class)
            ->shouldReceive("addToQueue")
            ->withArgs([$urlObj->id, $html])
            ->once();

        $queueUrlService = new QueueUrlService();
        $queueUrlService->process($urlObj->id);

        $newUrl = Url::find($urlObj->id);
        $this->assertNotNull($newUrl->last_refreshed);
    }

    public function testAddUrlObjToQueue()
    {
        $urlObj = Url::create(['url' => 'http:snodsberry.com', 'host' => 'snodsberry.com']);
        $jobCount = DB::table('jobs')->where('queue', 'urls')->count();

        $queueUrlService = new QueueUrlService();
        $queueUrlService->addUrlObjToQueue($urlObj);

        $this->assertEquals(1, DB::table('jobs')->where('queue', 'urls')->count() - $jobCount);
    }

    public function testReloadFirstOverflow()
    {
        $urlObj = Url::create(['url' => 'http://blueberry.com', 'host' => 'blueberry.com']);
        UrlOverflow::create(['url_id' => $urlObj->id]);

        $overflowCount = UrlOverflow::count();
        $jobCount = DB::table('jobs')->where('queue', 'urls')->count();

        $queueUrlService = new QueueUrlService();
        $queueUrlService->reloadFirstOverflow();

        $this->assertEquals(1,
            DB::table('jobs')->where('queue', 'urls')->count() -
            $jobCount
        );
        $this->assertEquals(-1, UrlOverflow::count() - $overflowCount);

    }

}
