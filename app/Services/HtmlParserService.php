<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class HtmlParserService
{
    /**
     * @param $url
     * @return string
     * @throws \Exception
     */
    public function getUrl($url)
    {
        $client = new Client();
        $res = $client->request('GET', $url);

        if ($this->isValidHtml($res)) {
            return $res->getBody()->getContents();
        }

    }

    /**
     * @param Response $res
     * @return bool
     * @throws \Exception
     */
    private function isValidHtml(Response $res)
    {
        $headers = $res->getHeaders();
        if (is_array($headers) && array_key_exists('Content-Type', $headers)) {
            if (is_array($headers['Content-Type'])) {
                foreach ($headers['Content-Type'] as $type) {
                    if (strpos($type, 'text/html') !== false) {
                        return true;
                    }

                }
            }
        }
        throw new \Exception("Invalid Headers");

    }
}
