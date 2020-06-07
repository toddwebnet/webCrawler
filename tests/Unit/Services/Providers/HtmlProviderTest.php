<?php

namespace Tests\Unit\Services\Providers;

use App\Helpers\Utils;
use App\Models\Html;
use App\Models\Link;
use App\Models\Url;
use App\Services\Providers\HtmlProvider;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HtmlProviderTest extends TestCase
{
    public function testAddToQueue()
    {
        $urlObj = factory(Url::class)->create();
        $html = Utils::randomChars(64);
        $queueCount = DB::table('jobs')->count();

        $htmlProvider = new HtmlProvider();
        $htmlProvider->addToQueue($urlObj->id, $html);

        $this->assertEquals(1, DB::table('jobs')->count() - $queueCount);
    }

    public function testInvalidateOldItems()
    {
        $htmlObj = factory(Html::class)->create();
        $linkObj = factory(Link::class)->create(['url_id' => $htmlObj->url_id]);
        $linkObj = Link::find($linkObj->id);
        $urlObj = Url::find($linkObj->url_id);

        $this->assertIsObject(Link::find($linkObj->id));
        $this->assertIsObject(Html::find($htmlObj->id));
        $this->assertIsObject(Url::find($urlObj->id));

        $htmlProvider = new HtmlProvider();
        $this->invokeMethod($htmlProvider, 'invalidateOldItems', [$urlObj->id]);

        $this->assertNull(Html::find($htmlObj->id));
        $this->assertNull(Link::find($linkObj->id));
        $this->assertIsObject(Url::find($urlObj->id));

    }

    public function testGetObj()
    {
        $urlObj = factory(Url::class)->create();
        $html = Utils::randomChars(64);
        $count = Html::count();
        $htmlProvider = new HtmlProvider();
        $htmlObj = $this->invokeMethod($htmlProvider, 'getObj', [$urlObj->id, $html]);

        $this->assertEquals(
            Html::where(['url_id' => $urlObj->id, 'html' => $html])->firstOrFail()->toArray(),
            Html::findOrFail($htmlObj->id)->toArray()
        );
        $this->assertEquals(1, Html::count() - $count);
    }

    public function testPopHtmlForProcessing()
    {
        $countUnprocessed = Html::where('process_status', HTML::UNPROCESSED)->count();
        $countFlagged = Html::where('process_status', HTML::FLAGGED)->count();
        $htmlProvider = new HtmlProvider();
        $html = $htmlProvider->popHtmlForProcessing();
        $this->assertEquals(-1, Html::where('process_status', HTML::UNPROCESSED)->count() - $countUnprocessed);
        $this->assertEquals(1, Html::where('process_status', HTML::FLAGGED)->count() - $countFlagged);
        $this->assertEquals($html->process_status, HTML::FLAGGED);

        DB::update('update htmls set process_status = 3');

        $html = $htmlProvider->popHtmlForProcessing();
        $this->assertNull($html);

        DB::update('update htmls set process_status = 0');

    }

    public function testMarkHtmlAsProcessed()
    {
        $countUnprocessed = Html::where('process_status', HTML::UNPROCESSED)->count();
        $countFlagged = Html::where('process_status', HTML::FLAGGED)->count();
        $countProcessed = Html::where('process_status', HTML::PROCESSED)->count();

        $htmlProvider = new HtmlProvider();
        $html = $htmlProvider->popHtmlForProcessing();
        $this->assertEquals(-1, Html::where('process_status', HTML::UNPROCESSED)->count() - $countUnprocessed);
        $this->assertEquals(1, Html::where('process_status', HTML::FLAGGED)->count() - $countFlagged);
        $this->assertEquals($html->process_status, HTML::FLAGGED);

        $html = $htmlProvider->markHtmlAsProcessed($html->id);
        $this->assertEquals(1, Html::where('process_status', HTML::PROCESSED)->count() - $countProcessed);
        $this->assertEquals($html->process_status, HTML::PROCESSED);

        $html = $htmlProvider->markHtmlAsProcessed(Html::max('id') + 1);
        $this->assertNull($html);
    }
}
