<?php

namespace Tests\Unit\Services;

use App\Services\BlacklistService;
use Tests\TestCase;

class BlacklistServiceTest extends TestCase
{
    public function testIsInBlacklist()
    {
        $blacklistService = new BlacklistService();
        $this->assertTrue($blacklistService->isInBlacklist('http://www.google.com/thd'));
        $this->assertTrue($blacklistService->isInBlacklist('http://google.com'));
        $this->assertTrue($blacklistService->isInBlacklist('http://facebook.com'));
        $this->assertTrue($blacklistService->isInBlacklist('http://www.facebook.com'));
        $this->assertTrue($blacklistService->isInBlacklist('http://happy.grumpy.facebook.com'));
        $this->assertTrue($blacklistService->isInBlacklist('http://wikipedia.org'));
        $this->assertFalse($blacklistService->isInBlacklist('http://adfasdfasdf.org'));
    }
}
