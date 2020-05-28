<?php

namespace App\Services\Queues;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class HttpService
{
    private $client;

    public function __construct(Client $client = null)
    {
        if ($client !== null) {
            $this->client = $client;
        } else {
            $this->client = new Client();
        }
    }

    public function getUrl($url)
    {
        /** @var Response $res */
        $res = $this->client->request('GET', $url);
        return $res->getBody();
    }
}
