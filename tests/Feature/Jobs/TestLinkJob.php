<?php

namespace Tests\Feature\Jobs;

use App\Jobs\LinkJob;
use App\Models\Link;
use App\Services\Queues\QueueLinkService;

class TestLinkJob extends JobTestCase
{
    public function testArgs()
    {
        $this->baseTestArgs(LinkJob::class, ['linkId' => 0]);
    }

    public function testHandle()
    {
        $this->setMock(QueueLinkService::class)
            ->shouldReceive('process')
            ->withArgs(
                factory(Link::class)->create()->id
            )
            ->once();
    }
}
