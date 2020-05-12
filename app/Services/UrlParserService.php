<?php

namespace App\Services;

class UrlParserService
{
    private $url;

    public function __construct($url)
    {
        $this->url = self::cleanUrl($url);

    }

    public static function cleanUrl($url)
    {
        $lcUrl = strtolower($url);
        $slashPos = strpos($lcUrl, '//');
        $httpPos = strpos($lcUrl, 'http');
        if (($slashPos === false || $slashPos > 0) && $httpPos === false) {
            $url = "http://{$url}";
        } else if ($slashPos == 0 || $httpPos > 0) {
            $url = "http:{$url}";
        }
        return $url;
    }

    public function buildFullLinkOnPage($url)
    {
        $parsedUrl = $this->parse();

        if (strpos($url, 'http') === 0) {
            return $url;
        }
        if (strpos($url, '//') === 0) {
            return "http:{$url}";
        }
        if (strpos($url, '/') === 0) {
            return "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$url}";
        }
        return "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$parsedUrl['path']}/{$url}";

    }

    public function parse()
    {
        $url = $this->url;
        $parsed = parse_url($url);
        if (!array_key_exists('path', $parsed)) {
            $parsed['path'] = '/';
        } else {
            $parsed['path'] = rtrim($parsed['path'], '/');
        }
        return $parsed;
    }

}
