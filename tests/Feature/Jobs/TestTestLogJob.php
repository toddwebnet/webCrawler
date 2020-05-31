<?php

namespace Tests\Feature\Jobs;

use App\Jobs\TestLogJob;

class TestTestLogJob extends JobTestCase
{
    public function testArgs()
    {
        $this->baseTestArgs(TestLogJob::class, ['logStatement' => $this->faker->sentence]);
    }

}
