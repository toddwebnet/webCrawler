<?php

namespace Tests\Unit\Services\Providers;

use App\Models\Url;
use App\Models\UrlSize;
use Tests\TestCase;
use Tests\TestHelperTrait;

class UrlProviderTest extends TestCase
{
    use TestHelperTrait;

    public function testPopToQueue()
    {
        $urlObj = factory(Url::class)->create();
        $this->fillUrlSizesTo(UrlSize::DAILY_DOWNLOAD_LIMIT - 1);
        $this->assertTrue(true);
    }

    public function testPopToQueueNullUrl()
    {
        $this->assertTrue(true);
    }

    public function testPopToQueueEmptyOverflow()
    {
        $this->assertTrue(true);
    }

    public function testPopToQueueNoAllowDownloads()
    {
        $this->assertTrue(true);
    }

}
