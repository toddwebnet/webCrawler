<?php

namespace Tests\Unit\Services\Queues;

use App\Models\Link;
use App\Models\Url;
use App\Services\Providers\UrlProvider;
use App\Services\Queues\QueueLinkService;
use Faker\Factory as Faker;
use Tests\TestCase;

class QueueLinkServiceTest extends TestCase
{
    public function testProcess1()
    {
        $faker = Faker::create();
        $domain = $faker->word;

        $urlObj = Url::create(['url' => 'http://' . $domain . '.com', 'host' => '' . $domain . '.com']);
        $link = $faker->url;
        $linkObj = Link::create([
            'url_id' => $urlObj->id,
            'link' => $link,
            'text' => $faker->sentence(3)
        ]);
        $this->setMock(UrlProvider::class)
            ->shouldReceive('addNewUrl')
            ->withArgs([$link])
            ->once();
        $queueLinkService = new QueueLinkService();
        $queueLinkService->process($linkObj->id);
    }

    public function testProcess2()
    {
        $faker = Faker::create();
        $domain = $faker->word;

        $urlObj = Url::create(['url' => 'http://' . $domain . '.com', 'host' => '' . $domain . '.com']);
        $link = $faker->url;
        $linkObj = Link::create([
            'url_id' => $urlObj->id,
            'link' => $link,
            'text' => $faker->sentence(3)
        ]);
        $this->setMock(UrlProvider::class)
            ->shouldReceive('addNewUrl')
            ->withNoArgs()
            ->never();
        $queueLinkService = new QueueLinkService();
        $queueLinkService->process($linkObj->id+1);
    }
}
