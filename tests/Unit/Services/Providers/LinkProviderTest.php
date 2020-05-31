<?php

namespace Tests\Unit\Services\Providers;

use App\Models\Link;
use App\Models\Url;
use App\Services\Providers\LinkProvider;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LinkProviderTest extends TestCase
{

    public function testAddToQueue()
    {
        $urlObj = factory(Url::class)->create();
        $urlId = $urlObj->id;
        $link = $this->faker->url;
        $text = $this->faker->sentence;
        $count = Link::count();
        $queueCount = DB::table('jobs')->count();

        $linkProvider = new LinkProvider();
        $linkProvider->addToQueue($urlId, $link, $text);;

        $this->assertEquals(1, Link::count() - $count);
        $this->assertEquals(1, DB::table('jobs')->count() - $queueCount);

    }

    public function testGetObj()
    {

        $urlObj = factory(Url::class)->create();
        $urlId = $urlObj->id;
        $link = $this->faker->url;
        $text = $this->faker->sentence;
        $count = Link::count();
        $linkProvider = new LinkProvider();
        $linkObj = $this->invokeMethod($linkProvider, 'getObj', [$urlId, $link, $text]);

        $expected = Link::where([
            'url_id' => $urlId,
            'link' => $link,
            'text' => $text
        ])->firstOrFail()->toArray();
        $actual = Link::find($linkObj->id)->toArray();

        $this->assertEquals(1, Link::count() - $count);
        $this->assertEquals($expected, $actual);

    }
}
