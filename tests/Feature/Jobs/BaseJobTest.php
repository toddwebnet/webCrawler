<?php

namespace Tests\Feature\Jobs;

use App\Jobs\BaseJob;
use Tests\TestCase;

class BaseJobTest extends TestCase
{

    public function testCheckKeys()
    {
        $baseJob = $this->getMockForAbstractClass(BaseJob::class);

        $requiredKeys = ['one', 'two', 'three'];

        $msg = 'Looking for Argument Error';
        try {

            $this->invokeMethod($baseJob, 'checkKeys', [
                [], $requiredKeys
            ]);
            $this->assertEquals('Expected Failure', $msg);
        } catch (\Exception $e) {

            $this->assertContains('not found in args', $e->getMessage());
            $this->assertContains('one, two, three', $e->getMessage());
            $this->assertEquals($msg, $msg);
        }

        try {

            $this->invokeMethod($baseJob, 'checkKeys', [
                ['four' => 4], $requiredKeys
            ]);
            $this->assertEquals('Expected Failure', $msg);
        } catch (\Exception $e) {
            $this->assertContains('one, two, three', $e->getMessage());
            $this->assertContains('not found in args', $e->getMessage());
            $this->assertEquals($msg, $msg);
        }

        try {

            $this->invokeMethod($baseJob, 'checkKeys', [
                ['one' => 1, 'two' => 2, 'four' => 4], $requiredKeys
            ]);
            $this->assertNotContains('one, two, three', $e->getMessage());
            $this->assertContains('three', $e->getMessage());
            $this->assertEquals('Expected Failure', $msg);
        } catch (\Exception $e) {

            $this->assertContains('not found in args', $e->getMessage());
            $this->assertEquals($msg, $msg);
        }

        $msg = 'Not expecting an error';
        try {
            $this->invokeMethod($baseJob, 'checkKeys', [
                ['one' => 1, 'two' => 2, 'three' => 3], $requiredKeys
            ]);
            $this->assertEquals($msg, $msg);
        } catch (\Exception $e) {
            $this->assertEquals($msg,
                'I got an error');
        }
        
        $msg = 'Not expecting an error';
        try {
            $this->invokeMethod($baseJob, 'checkKeys', [
                ['one' => 1, 'two' => 2, 'three' => 3, 'four' => 4], $requiredKeys
            ]);
            $this->assertEquals($msg, $msg);
        } catch (\Exception $e) {
            $this->assertEquals($msg, 'I got an error');
        }

    }
}

