<?php

namespace Tests\Unit\Services;

use App\Services\PathNameService;

class PathNameServiceTest
{
    public function testGetRandomPathName()
    {
        $checks = [];
        $pathNameService = new PathNameService();

        for ($x = 0; $x < 50; $x++) {
            $rand = $pathNameService->getRandomPathName();
            $this->assertEquals(40, strlen($rand));
            $this->assertFalse(in_array($rand, $checks));
            $checks[] = $rand;
        }

    }

}
