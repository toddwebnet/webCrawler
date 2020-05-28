<?php

namespace Tests\Unit\Services;

use App\Models\Url;
use App\Models\UrlSizes;
use App\Services\HtmlParserService;
use App\Services\S3StorageService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Tests\TestCase;

class HtmlParserServiceTest extends TestCase
{

    public function testGetS3Url()
    {
        $host = 'host';
        $url = "http://whatwhere.com";
        $size = 1;
        $contentType = 'text/html';
        $options = ['validate', 'log_sizes'];
        $times = [
            'getSize' => 1,
            'getBody' => 1,
            'getHeaders' => 1,
            'request' => 1,
        ];
        list($urlObject, $stream) = $this->buildGetUrlMocks($host, $url, $size, $contentType, $times);
        $count = UrlSizes::count();
        $htmlParserService = new HtmlParserService();

        $return = ['key' => 'some_value'];

        $s3StorageService = $this->setMock(S3StorageService::class)
            ->shouldReceive('putObject')
            ->withArgs([$stream])
            ->once()
            ->andReturn($return);
        $htmlParserService = new HtmlParserService();

        $result = $htmlParserService->getS3Url($urlObject, $options);
        $this->assertEquals($return['key'], $result);
        $this->assertEquals(1, UrlSizes::count() - $count);
    }

    public function testGetUrl1()
    {
        $host = 'host';
        $url = "http://whatwhere.com";
        $size = 1;
        $contentType = 'text/html';
        $options = [];
        $times = [
            'getSize' => 1,
            'getBody' => 1,
            'getHeaders' => 0,
            'request' => 1,
        ];
        list($urlObject, $stream) = $this->buildGetUrlMocks($host, $url, $size, $contentType, $times);
        $htmlParserService = new HtmlParserService();
        $result = $htmlParserService->getUrl($urlObject, $options);
        $this->assertEquals($stream, $result);
    }

    public function testGetUrl2()
    {
        $host = 'host';
        $url = "http://whatwhere.com";
        $size = 1;
        $contentType = 'text/html';
        $options = ['validate'];
        $times = [
            'getSize' => 1,
            'getBody' => 1,
            'getHeaders' => 1,
            'request' => 1,
        ];
        list($urlObject, $stream) = $this->buildGetUrlMocks($host, $url, $size, $contentType, $times);
        $htmlParserService = new HtmlParserService();
        $result = $htmlParserService->getUrl($urlObject, $options);
        $this->assertEquals($stream, $result);
    }

    public function testGetUrl3()
    {
        $host = 'host';
        $url = "http://whatwhere.com";
        $size = 1;
        $contentType = 'text/png';
        $options = ['validate'];
        $times = [
            'getSize' => 0,
            'getBody' => 0,
            'getHeaders' => 1,
            'request' => 1,
        ];
        list($urlObject, $stream) = $this->buildGetUrlMocks($host, $url, $size, $contentType, $times);
        $htmlParserService = new HtmlParserService();
        try {
            $result = $htmlParserService->getUrl($urlObject, $options);
            $this->assertEquals('I expected to throw an error', '');
        } catch (\Exception $e) {
            $this->assertContains('Invalid Html in Url', $e->getMessage());
        }

    }

    public function testGetUrl4()
    {
        $host = 'host';
        $url = "http://whatwhere.com";
        $size = 1;
        $contentType = 'text/html';
        $options = ['validate', 'log_sizes'];
        $times = [
            'getSize' => 1,
            'getBody' => 1,
            'getHeaders' => 1,
            'request' => 1,
        ];
        $count = UrlSizes::count();
        list($urlObject, $stream) = $this->buildGetUrlMocks($host, $url, $size, $contentType, $times);
        $htmlParserService = new HtmlParserService();
        $result = $htmlParserService->getUrl($urlObject, $options);
        $this->assertEquals($stream, $result);
        $this->assertEquals(1, UrlSizes::count() - $count);
    }

    public function testGetUrl5()
    {
        $host = 'host';
        $url = "http://whatwhere.com";
        $size = 1024 * 1024 + 1;
        $contentType = 'text/html';
        $options = [];
        $times = [
            'getSize' => 1,
            'getBody' => 1,
            'getHeaders' => 0,
            'request' => 1,
        ];
        list($urlObject, $stream) = $this->buildGetUrlMocks($host, $url, $size, $contentType, $times);
        $htmlParserService = new HtmlParserService();
        try {
            $result = $htmlParserService->getUrl($urlObject, $options);
            $this->assertEquals('I expected to throw an error', '');
        } catch (\Exception $e) {
            $this->assertContains('Data Too Big, skipping', $e->getMessage());
        }
    }

    private function buildGetUrlMocks($host, $url, $size, $contentType, $times)
    {

        $urlObject = Url::create([
            'host' => $host,
            'url' => $url
        ]);
        $stream = $this->setMock(Stream::class)
            ->shouldReceive('getSize')
            ->withArgs([])
            ->times($times['getSize'])
            ->andReturn($size)
            ->getMock();;

        $response = $this->setMock(Response::class)
            ->shouldReceive('getBody')
            ->withArgs([])
            ->times($times['getBody'])
            ->andReturn($stream)
            ->getMock();

        $response->shouldReceive('getHeaders')
            ->withArgs([])
            ->times($times['getHeaders'])
            ->andReturn(['Content-Type' => [$contentType]])
            ->getMock();

        $client = $this->setMock(Client::class)
            ->shouldReceive('request')
            ->withArgs(['GET', $url])
            ->times($times['request'])
            ->andReturn($response)
            ->getMock();
        return [$urlObject, $stream];
    }

    public function testSaveBodyToS3()
    {
        $return = ['key' => 'some_value'];
        $stream = $this->setMock(Stream::class);
        $s3StorageService = $this->setMock(S3StorageService::class)
            ->shouldReceive('putObject')
            ->withArgs([$stream])
            ->once()
            ->andReturn($return);
        $htmlParserService = new HtmlParserService();
        $this->assertEquals(
            $return['key'],
            $this->invokeMethod($htmlParserService, 'saveBodyToS3', ['stream' => $stream])
        );

    }

    public function testIsValidHtml()
    {
        $res = $this->setMock(Response::class);

        $tests = [
            [
                'return' => ['Content-Type' => ['text/html']],
                'expected' => true
            ],
            [
                'return' => ['Content-Type' => ['text/pdf']],
                'expected' => false
            ],
            [
                'return' => null,
                'expected' => false
            ],
            [
                'return' => [],
                'expected' => false
            ]
        ];

        foreach ($tests as $test) {
            $htmlParserService = new HtmlParserService();
            $res->shouldReceive('getHeaders')
                ->withArgs([])
                ->once()
                ->andReturn($test['return']);
            $this->assertEquals($test['expected'],
                $this->invokeMethod($htmlParserService, 'isValidHtml', ['res' => $res])
            );
        }
    }
}
