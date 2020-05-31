<?php

namespace Tests\Unit\Services;

use App\Services\UrlParserService;
use Tests\TestCase;

class UrlParserServiceTest extends TestCase
{
    public function testParse()
    {
        $tests = [
            'http://domain.com/site/site/file.html' => [
                'scheme' => 'http',
                'host' => 'domain.com',
                'path' => '/site/site/file.html'
            ],
            'http://domain.com/site/site/file2.html' => [
                'path' => '/site/site/file2.html',
                'scheme' => 'http',
                'host' => 'domain.com',
            ],
            'http://stupid.org' => [
                'scheme' => 'http',
                'host' => 'stupid.org',
                'path' => '/'
            ],
            'https://stupid.org' => [
                'scheme' => 'https',
                'host' => 'stupid.org',
                'path' => '/'
            ],
        ];
        foreach ($tests as $input => $expected) {
            $urlParserService = new UrlParserService($input);
            $this->assertEquals($expected, $urlParserService->parse());
        }

    }

    public function testCleanUrl()
    {

        $tests = [
            'http://dog.com/stuf/stuf/file.html' => 'http://dog.com/stuf/stuf/file.html',
            'https://dog.com/gnaw/gnaw/bone.asp' => 'https://dog.com/gnaw/gnaw/bone.asp',
            '//server.com/something' => 'http://server.com/something',
            'site.com/somewhere/out/there' => 'http://site.com/somewhere/out/there',
            null => 'http://',
            4 => 'http://4'
        ];

        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected, UrlParserService::cleanUrl($input));
        }

        $errors = [
            ['this'],
            (object)['that'],

        ];
        foreach ($errors as $input) {
            try {
                UrlParserService::cleanUrl($input);
                $this->assertEquals('Expected to catch error', null);
            } catch (\Exception $e) {
                $this->assertEquals('Caught Error', 'Caught Error');

            }
        }
    }

    public function testBuildFullLinkOnPage()
    {
        $tests = [
            [
                'url' => 'http://domain.com',
                'link' => '/site',
                'expected' => 'http://domain.com/site'
            ],
            [
                'url' => 'http://domain.com/folder',
                'link' => '../folder2/file2.txt',
                'expected' => 'http://domain.com/folder2/file2.txt'
            ],
            [
                'url' => 'http://domain.com/folder/file.txt',
                'link' => '../folder2/file2.txt',
                'expected' => 'http://domain.com/folder2/file2.txt'
            ],
            [
                'url' => 'http://domain.com/this/folder/page.html',
                'link' => '/unga/bunga/file.txt',
                'expected' => 'http://domain.com/unga/bunga/file.txt'
            ],
            [
                'url' => 'http://domain.com',
                'link' => 'http://domain2.com',
                'expected' => 'http://domain2.com'
            ],
            [
                'url' => 'http://domain.com',
                'link' => 'https://domain2.com',
                'expected' => 'https://domain2.com'
            ],
        ];
        foreach ($tests as $test) {
            $urlParserService = new UrlParserService($test['url']);
            $this->assertEquals(
                $test['expected'],
                $urlParserService->buildFullLinkOnPage($test['link'])
            );

        }

    }

    public function testBuildRelativeUrl()
    {
        $tests = [
            [
                'url' => 'http://domain.com',
                'link' => './site',
                'expected' => 'http://domain.com/site'
            ],
            [
                'url' => 'http://domain.com/folder',
                'link' => '../folder2/file2.txt',
                'expected' => 'http://domain.com/folder2/file2.txt'
            ],
            [
                'url' => 'http://domain.com/folder/file.txt',
                'link' => '../folder2/file2.txt',
                'expected' => 'http://domain.com/folder2/file2.txt'
            ]
        ];
        foreach ($tests as $test) {
            $urlParserService = new UrlParserService($test['url']);

            $this->assertEquals(
                $test['expected'],
                $urlParserService->buildRelativeUrl($test['link'])
            );
        }

    }

    public function testChopLastSlash()
    {
        $urlParserService = new UrlParserService("http://joe.com");
        $tests = [
            'http://joe.com/path/dog/' => 'http://joe.com/path/dog',
            'http://joe.com/path/dog' => 'http://joe.com/path',
            'http://joe.com/' => 'http://joe.com',
            'http://joe.com' => 'http://joe.com',
        ];
        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected,
                $this->invokeMethod($urlParserService, 'chopLastSlash', ['url' => $input])
            );
        }
    }

    public function testAddPreSlash()
    {
        $urlParserService = new UrlParserService("http://joe.com");

        $tests = [
            'path' => '/path',
            '/path' => '/path',
            '' => ''
        ];
        foreach ($tests as $input => $expected) {
            $this->assertEquals($expected,
                $this->invokeMethod($urlParserService, 'addPreSlash', ['link' => $input])
            );
        }
    }

}
