<?php

namespace App\Services;

use App\Models\UrlSizes;
use Aws\Result;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

class HtmlParserService
{
    /**
     * @param $url
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function getS3Url($url, $options = [])
    {
        return $this->saveBodyToS3(
            $this->getUrl($url, $options)
        );
    }

    /**
     * @param $url
     * @return string
     * @throws \Exception
     */
    public function getUrl($url, $options = [])
    {
        $client = app()->make(Client::class);
        $res = $client->request('GET', $url->url);

        if (in_array('validate', $options) && !$this->isValidHtml($res)) {
            throw new \Exception("Invalid Html in Url");
        }
        $bodyStream = $res->getBody();
        $size = $bodyStream->getSize();
        if ($size > 1024 * 1024) {
            throw new \Exception("Data Too Big, skipping");
        }
        if (in_array('log_sizes', $options)) {

            UrlSizes::create([
                'url_id' => $url->id,
                'size' => $size,
                'timestamp' => time(),
            ]);
        }
        return $bodyStream;
    }

    /**
     * @param Stream $stream
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function saveBodyToS3(Stream $stream)
    {
        /** @var Result $result */
        $result = app()->make(S3StorageService::class)->putObject($stream);
        return $result['key'];
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
        return false;
    }
}
