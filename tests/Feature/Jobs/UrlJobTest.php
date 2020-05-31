<?php

namespace Tests\Feature\Jobs;

use App\Jobs\UrlJob;
use App\Models\Url;
use App\Services\Queues\QueueUrlService;

class UrlJobTest extends JobTestCase
{

    public function testArgs()
    {
        $this->baseTestArgs(UrlJob::class, ['urlId' => 0]);
    }

    public function testHandle(){

        $objUrl = factory(Url::class)->create();
        $this->setMock(QueueUrlService::class)
            ->shouldReceive('process')
            ->withArgs([$objUrl->id])
            ->once();

        $job = new UrlJob(['urlId' => $objUrl->id]);
        $job->handle();
    }
}
