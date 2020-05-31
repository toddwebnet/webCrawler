<?php

namespace Tests\Unit\Services\Providers;

use App\Models\Url;
use App\Models\UrlOverflow;
use App\Models\UrlSize;
use App\Services\Providers\UrlProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UrlProviderTest extends TestCase
{

    public function testAddNewUrlInsert()
    {
        $url = $this->faker->url;
        $count = Url::count();
        $overflowCount = UrlOverflow::count();

        $urlProvider = new UrlProvider();
        $urlObj = $urlProvider->addNewUrl($url);

        $findUrl = Url::findUrl($url);

        $this->assertEquals($findUrl->toArray(), Url::find($urlObj->id)->toArray());
        $this->assertEquals(1, Url::count() - $count);
        $this->assertEquals(1, UrlOverflow::count() - $overflowCount);
    }

    public function testAddNewUrlNoInsert()
    {
        $urlObj = factory(Url::class)->create();
        $urlObj = Url::find($urlObj->id);
        $url = $urlObj->url;
        $count = Url::count();
        $overflowCount = UrlOverflow::count();

        $urlProvider = new UrlProvider();
        $urlObj = $urlProvider->addNewUrl($url);

        $findUrl = Url::findUrl($url);

        $this->assertEquals($findUrl->toArray(), Url::find($urlObj->id)->toArray());
        $this->assertEquals(0, Url::count() - $count);
        $this->assertEquals(1, UrlOverflow::count() - $overflowCount);
    }

    public function testAddNewUrlNoInsertNoOverflow()
    {
        $urlObj = factory(Url::class)->create();

        $urlObj = Url::find($urlObj->id);
        $urlObj->last_refreshed = new Carbon();
        $urlObj->save();

        $url = $urlObj->url;
        $count = Url::count();
        $overflowCount = UrlOverflow::count();

        $urlProvider = new UrlProvider();
        $urlObj = $urlProvider->addNewUrl($url);

        $findUrl = Url::findUrl($url);

        $this->assertEquals($findUrl->toArray(), Url::find($urlObj->id)->toArray());
        $this->assertEquals(0, Url::count() - $count);
        $this->assertEquals(0, UrlOverflow::count() - $overflowCount);
    }

    public function testGetObjInsert()
    {
        $url = $this->faker->url;
        $count = Url::count();

        $urlProvider = new UrlProvider();
        $urlObj2 = $this->invokeMethod($urlProvider, 'getObj', ['url' => $url]);
        $urlObj = Url::findUrl($url);
        $this->assertEquals($urlObj->toArray(), Url::find($urlObj2->id)->toArray());
        $this->assertEquals(1, Url::count() - $count);
    }

    public function testGetObjNoInsert()
    {
        $urlObj = factory(Url::class)->create();
        $urlObj = Url::find($urlObj->id);
        $count = Url::count();

        $urlProvider = new UrlProvider();
        $urlObj2 = $this->invokeMethod($urlProvider, 'getObj', ['url' => $urlObj->url]);

        $this->assertEquals($urlObj->toArray(), $urlObj2->toArray());
        $this->assertEquals(0, Url::count() - $count);

    }

    public function testAddUrlObjToQueue()
    {
        $urlObj = factory(Url::class)->create();
        $queueCount = DB::table('jobs')->count();
        $lastRefreshed = $urlObj->last_refreshed;

        $urlProvider = new UrlProvider();
        $urlProvider->addUrlObjToQueue($urlObj);

        $this->assertNull($lastRefreshed);
        $this->assertNotNull(Url::find($urlObj->id)->last_refreshed);
        $this->assertEquals(1, DB::table('jobs')->count() - $queueCount);

    }

    public function testPopToQueue()
    {
        $urlObj = factory(Url::class)->create();
        factory(UrlOverflow::class)->create(['url_id' => $urlObj->id]);

        $count = UrlOverflow::all()->count();
        $queueCount = DB::table('jobs')->count();

        $urlProvider = new UrlProvider();
        $urlProvider->popToQueue();

        $this->assertEquals(1, $count - UrlOverflow::all()->count());
        $this->assertEquals(1, DB::table('jobs')->count() - $queueCount);
    }

    public function testPopToQueueNullUrl()
    {
        $urlObj = factory(Url::class)->create();
        UrlOverflow::truncate();
        factory(UrlOverflow::class)->create(['url_id' => $urlObj->id + 10000]);

        $count = UrlOverflow::all()->count();
        $queueCount = DB::table('jobs')->count();

        $urlProvider = new UrlProvider();
        $urlProvider->popToQueue();

        $this->assertEquals(1, $count - UrlOverflow::all()->count());
        $this->assertEquals(0, DB::table('jobs')->count() - $queueCount);

    }

    public function testPopToQueueEmptyOverflow()
    {

        $urlObj = factory(Url::class)->create();
        UrlOverflow::truncate();

        $count = UrlOverflow::all()->count();
        $queueCount = DB::table('jobs')->count();

        $urlProvider = new UrlProvider();
        $urlProvider->popToQueue();

        $this->assertEquals(0, $count - UrlOverflow::all()->count());
        $this->assertEquals(0, DB::table('jobs')->count() - $queueCount);
    }

    public function testPopToQueueNoAllowDownloads()
    {

        $urlObj = factory(Url::class)->create();
        factory(UrlOverflow::class)->create(['url_id' => $urlObj->id]);

        factory(UrlSize::class)->create([
            'size' => UrlSize::DAILY_DOWNLOAD_LIMIT + 100
        ]);

        $count = UrlOverflow::all()->count();
        $queueCount = DB::table('jobs')->count();

        $urlProvider = new UrlProvider();
        $urlProvider->popToQueue();

        $this->assertEquals(0, $count - UrlOverflow::all()->count());
        $this->assertEquals(0, DB::table('jobs')->count() - $queueCount);
    }

}
