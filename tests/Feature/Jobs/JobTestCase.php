<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;

class JobTestCase extends TestCase
{
    protected function baseTestArgs($class, $args)
    {

        $msg = 'Looking for Argument Error';
        try {
            new $class();
            $this->assertEquals('Expected Failure', $msg);
        } catch (\ArgumentCountError $e) {
            $this->assertEquals($msg, $msg);
        }

        $msg = 'Looking for Keys Exception';
        try {
            new $class([
                'nothing' => null
            ]);
            $this->assertEquals('Expected Failure', $msg);
        }catch(\Exception $e){
            $this->assertContains('not found in args', $e->getMessage());
            $this->assertEquals($msg, $msg);
        }
        $msg = 'Not expecting an error';
        try{
            new $class($args);
            $this->assertEquals($msg, $msg);
        }catch(\Exception $e){
            $this->assertEquals($msg, 'I got an error');
        }
    }
}
