<?php

namespace App\Services;

class UrlParserService
{
    private $url;

    /**
     * UrlParserService constructor.
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = self::cleanUrl($url);
    }

    /**
     * @return array|false|int|string|null
     */
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

    /**
     * @param $url
     * @return string
     */
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

    /**
     * @param $url
     * @return string
     */
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
        if (strpos($url, '.') === 0) {
            return $this->buildRelativeUrl($url);
        }
        return "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$parsedUrl['path']}/{$url}";

    }

    /**
     * @param $link
     * @return string
     */
    public function buildRelativeUrl($link)
    {
        $parsed = $this->parse();
        $baseUrl = $this->chopLastSlash($parsed['path']);
        while (strpos($link, '.') === 0) {
            if (strpos($link, '../') === 0) {
                $baseUrl = $this->chopLastSlash($baseUrl);
                $link = $this->addPreSlash(substr($link, 3));
            } elseif (strpos($link, './') === 0) {
                $link = $this->addPreSlash(substr($link, 2));
            }
        }
        return "{$parsed['scheme']}://{$parsed['host']}{$baseUrl}{$link}";
    }

    /**
     * @param $url
     * @return false|string
     */
    private function chopLastSlash($url)
    {
        $slashPos = strrpos(
            str_replace('://', ':::', $url)
            , '/');
        if ($slashPos === false) {
            return $url;
        } else {
            return (substr($url, 0, $slashPos));
        }
    }

    /**
     * @param $link
     * @return string
     */
    private function addPreSlash($link)
    {
        if ($link == '') {
            return $link;
        }
        $slashPos = strrpos($link, '/');
        if ($slashPos !== 0) {
            $link = '/' . $link;
        }
        return $link;
    }

}
