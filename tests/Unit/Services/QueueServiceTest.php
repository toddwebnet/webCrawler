<?php

namespace Tests\Unit\Services;

use App\Jobs\TestLogJob;
use App\Services\QueueService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueueServiceTest extends TestCase
{
    public function testSendToQueue()
    {
        $class = TestLogJob::class;
        $args = ['logStatement' => 'something'];
        $count = DB::table('jobs')->count();
        $queueService = new QueueService();
        $queueService->sendToQueue($class, $args);
        $this->assertEquals(1, DB::table('jobs')->count() - $count);
    }

    public function testSendToQueue2()
    {
        $class = TestLogJob::class;
        $args = ['logStatement' => 'something'];
        $count = DB::table('jobs')->where('queue', 'tests')->count();
        $queueService = new QueueService();
        $queueService->sendToQueue($class, $args);
        $this->assertEquals(1, DB::table('jobs')->where('queue', 'tests')->count() - $count);
    }


    public function testSendToQueue3()
    {
        $class = TestLogJob::class;
        $args = ['logStatement' => 'something'];
        $count = DB::table('jobs')->where('queue', 'urls')->count();
        $queueService = new QueueService();
        $queueService->sendToQueue($class, $args, 'urls');
        $this->assertEquals(1, DB::table('jobs')->where('queue', 'urls')->count() - $count);
    }
}
