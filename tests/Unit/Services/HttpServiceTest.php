<?php

namespace Tests\Unit\Services;

use App\Services\Queues\HttpService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class HttpServiceTest extends TestCase
{
    public function testGetUrl()
    {
        $url = "http://dog.com";

        $response = $this->setMock(Response::class)
            ->shouldReceive('getBody')
            ->withArgs([])
            ->once()
            ->andReturn('test')
            ->getMock();

        $client = $this->setMock(Client::class)
            ->shouldReceive('request')
            ->withArgs(['GET', $url])
            ->once()
            ->andReturn($response)
            ->getMock();

        $httpService = new HttpService($client);
        $this->assertEquals('test', $httpService->getUrl($url));

    }

}
