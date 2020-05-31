<?php

namespace Tests\Feature\Jobs;

use App\Jobs\HtmlJob;
use App\Models\Html;
use App\Services\Queues\QueueHtmlService;

class TestHtmJob extends JobTestCase
{
    public function testArgs()
    {
        $this->baseTestArgs(HtmlJob::class, ['htmlId' => 0]);
    }

    public function testHandle()
    {
        $this->setMock(QueueHtmlService::class)
            ->shouldReceive('process')
            ->withArgs(
                factory(Html::class)->create()->id
            )
            ->once();

    }
}
