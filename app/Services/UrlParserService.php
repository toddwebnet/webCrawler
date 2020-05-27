<?php

namespace App\Services;

use App\Helpers\Utils;

class UrlParserService
{
    private $url;

    public function __construct($url)
    {
        $this->url = self::cleanUrl($url);

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
        Utils::logToFile("{$this->url}\t{$url}");
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
        if (strpos($url, '.') === 0) {
            return $this->buildRelativeUrl($url);
        }
        return "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$parsedUrl['path']}/{$url}";

    }

    public function buildRelativeUrl($link)
    {
        $parsed = $this->parse();
        $baseUrl = $this->chopLastSlash($parsed['path']);
        while (strpos($link, '.') == 0) {
            if (strpos($link, '../') == 0) {
                $baseUrl = $this->chopLastSlash($baseUrl);
                $link = substr($link, 3);
            } elseif (strpos($link, './') == 0) {
                $link = substr($link, 3);
            }
        }
        return "{$parsed['scheme']}://{$parsed['host']}{$baseUrl}{$link}";
    }

    private function chopLastSlash($url)
    {
        $slashPos = strrpos($url, '/');
        return (substr($url, 0, $slashPos));
    }

}
