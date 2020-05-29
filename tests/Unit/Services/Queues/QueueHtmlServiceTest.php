<?php

namespace Tests\Unit\Services\Queues;

use App\Models\Html;
use App\Models\Url;
use App\Services\Queues\QueueHtmlService;
use App\Services\S3StorageService;
use Faker\Factory as Faker;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\DB;
use PHPHtmlParser\Dom;
use Tests\TestCase;

class QueueHtmlServiceTest extends TestCase
{

    public function testProcess()
    {
        $faker = Faker::create();
        $url = $faker->url;
        $parsed = parse_url($url);
        $urlObj = Url::create(['url' => $url, 'host' => $parsed['host']]);
        $key = $faker->randomNumber(6);

        $htmlObj = Html::create(['url_id' => $urlObj->id, 'html' => $key]);
        $html = $faker->randomHtml();
        $links = [];
        $expectedCount = 1;
        for ($x = 0; $x < $expectedCount; $x++) {
            $links[] = (object)['href' => $faker->url, 'text' => $faker->sentence];
            $links[] = (object)['href' => $faker->url . '.png', 'text' => $faker->sentence];
        }

        $countQueue = DB::table('jobs')->where('queue', 'links')->count();

        $bodyStream = $this->setMock(Stream::class)
            ->shouldReceive('getContents')
            ->andReturn($html)
            ->getMock();

        $this->setMock(S3StorageService::class)
            ->shouldReceive('getObject')
            ->withArgs([$key])
            ->once()
            ->andReturn($bodyStream);

        $domObj = $this->setMock(Dom::class);
        $domObj->shouldReceive('load')
            ->withAnyArgs()
            ->once();
        $domObj->shouldReceive('find')
            ->withArgs(['a'])
            ->once()
            ->andReturn($links);

        $queueHtmlService = new QueueHtmlService();
        $queueHtmlService->process($htmlObj->id);

        $this->assertEquals($expectedCount, DB::table('jobs')->where('queue', 'links')->count() - $countQueue);

    }

    public function testProcess2()
    {
        $faker = Faker::create();
        $url = $faker->url;
        $parsed = parse_url($url);
        $urlObj = Url::create(['url' => $url, 'host' => $parsed['host']]);
        $key = $faker->randomNumber(6);

        $htmlObj = Html::create(['url_id' => $urlObj->id, 'html' => $key]);
        $html = $faker->randomHtml();
        $links = [];
        $expectedCount = 1;
        for ($x = 0; $x < $expectedCount; $x++) {
            $links[] = (object)['href' => $faker->url, 'text' => $faker->sentence];
            $links[] = (object)['href' => $faker->url . '.png', 'text' => $faker->sentence];
        }

        $countQueue = DB::table('jobs')->where('queue', 'links')->count();

        $bodyStream = $this->setMock(Stream::class)
            ->shouldReceive('getContents')
            ->withArgs([])
            ->never()
            ->andReturn($html)
            ->getMock();

        $this->setMock(S3StorageService::class)
            ->shouldReceive('getObject')
            ->withArgs([$key])
            ->never()
            ->andReturn($bodyStream);

        $domObj = $this->setMock(Dom::class);
        $domObj->shouldReceive('load')
            ->withAnyArgs()
            ->never();

        $domObj->shouldReceive('find')
            ->withArgs(['a'])
            ->never()
            ->andReturn($links);

        $queueHtmlService = new QueueHtmlService();
        $queueHtmlService->process($htmlObj->id + 1);

        $this->assertEquals(0, DB::table('jobs')->where('queue', 'links')->count() - $countQueue);

    }

    public function testProcessHtml()
    {
        $faker = Faker::create();
        $url = $faker->url;
        $parsed = parse_url($url);
        $urlObj = Url::create(['url' => $url, 'host' => $parsed['host']]);
        $key = $faker->randomNumber(6);
        $htmlObj = Html::create(['url_id' => $urlObj->id, 'html' => $key]);
        $html = $faker->randomHtml();
        $links = [];
        $expectedCount = 15;
        for ($x = 0; $x < $expectedCount; $x++) {
            $links[] = (object)['href' => $faker->url, 'text' => $faker->sentence];
            $links[] = (object)['href' => $faker->url . '.png', 'text' => $faker->sentence];
        }
        $expectedCount++;
        $newFakeUrl = $faker->url;
        $links[] = (object)['href' => $newFakeUrl, 'text' => $faker->sentence];
        $links[] = (object)['href' => $newFakeUrl, 'text' => $faker->sentence];
        $links[] = (object)['href' => $newFakeUrl, 'text' => $faker->sentence];
        $links[] = (object)['href' => $newFakeUrl, 'text' => $faker->sentence];
        $links[] = (object)['href' => $newFakeUrl, 'text' => $faker->sentence];

        $countQueue = DB::table('jobs')->where('queue', 'links')->count();

        $bodyStream = $this->setMock(Stream::class)
            ->shouldReceive('getContents')
            ->withArgs([])
            ->once()
            ->andReturn($html)
            ->getMock();

        $this->setMock(S3StorageService::class)
            ->shouldReceive('getObject')
            ->withArgs([$key])
            ->once()
            ->andReturn($bodyStream);

        $domObj = $this->setMock(Dom::class);
        $domObj->shouldReceive('load')
            ->withAnyArgs()
            ->once();
        $domObj->shouldReceive('find')
            ->withArgs(['a'])
            ->once()
            ->andReturn($links);

        $queueHtmlService = new QueueHtmlService();
        $queueHtmlService->processHtml($htmlObj);

        $this->assertEquals($expectedCount, DB::table('jobs')->where('queue', 'links')->count() - $countQueue);

    }

    public function testIsValidLink()
    {
        $faker = Faker::create();
        $fails = [
            'jpg', 'jpeg', 'png', 'mp4', 'mpg', 'mp3', '7z', 'zip',
            'msi', 'exe', 'arj', 'ace', 'tar', 'gz', 'iso', 'img', 'dmg',
            'gif', 'xml', 'tif', 'bmp', 'mdb', 'sql', 'dat', 'sqlite',
            'pub', 'doc', 'docx', 'xls', 'xlsx', 'mdbx', 'log', 'txt', 'md',
            'pdf', 'asc', 'ascii', 'gpx', 'gml', 'rom', 'ico', 'raw', 'ai', 'psd',
            'eps', 'vod', 'lnk', 'webloc', 'odf', 'obj', 'class', 'dll', 'jar', 'war',
            'ps', 'pnp', 'ppt', 'pptx', 'js', 'javascript', 'au3', 'bat', 'vox', 'voc',
            'ram', 'm3u', 'asx', 'avi', 'fla', 'm4v', 'ogg'
        ];
        $passes = [
            'asp', 'aspx', 'html', 'htm', 'php', 'phtml'
        ];
        $queueHtmlService = new QueueHtmlService();
        $this->assertFalse($queueHtmlService->isValidLink("#" . $faker->word));
        $this->assertFalse($queueHtmlService->isValidLink("JavaScript:alert('hi')"));
        foreach ($fails as $fail) {
            $this->assertFalse($queueHtmlService->isValidLink(
                $faker->url . "." . $fail
            ));
        }
        foreach ($passes as $pass) {
            $this->assertTrue($queueHtmlService->isValidLink(
                $faker->url . "." . $pass
            ));
        }
        $this->assertTrue($queueHtmlService->isValidLink(
            $faker->url
        ));

    }
}
