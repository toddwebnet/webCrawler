<?php

namespace App\Services\Queues;

use App\Helpers\Utils;
use App\Models\Html;
use App\Models\QueueHtml;
use App\Models\Url;
use App\Services\Providers\LinkProvider;
use App\Services\S3StorageService;
use App\Services\UrlParserService;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;

class QueueHtmlService
{

    protected $processedLinks = [];

    public function process($htmlId)
    {

        $html = Html::find($htmlId);
        if ($html === null) {
            Log::warning(get_class($this) . "->process({$htmlId}) - Html Not Found");
            return;
        }
        $this->processHtml($html);
        return 1;
    }

    public function processHtml($html)
    {
        $url = Url::find($html->url_id);
        $urlParser = app()->make(UrlParserService::class, ['url' => $url->url]);
        /** @var Stream $bodyStream */
        $bodyStream = app()->make(S3StorageService::class)
            ->getObject($html->html);

        $dom = new Dom();
        $dom->load(
            utf8_encode($bodyStream->getContents())
        );

        $links = $dom->find('a');
        $linkProvider = app()->make(LinkProvider::class);

        foreach ($links as $link) {

            if ($link->href &&
                $this->isValidLink($link->href)
            ) {

                $link->href = $urlParser->buildFullLinkOnPage(
                    str_replace(' ', '+',
                        trim($link->href)
                    )
                );

                if (!in_array($link->href, $this->processedLinks)) {
                    $this->processedLinks[] = $link->href;
                    Log::info('Adding to Link Queue: ' . $link->href);
                    $linkProvider->addToQueue(
                        $url->id,
                        $link->href,
                        $link->text
                    );
                }
            }
        }

    }

    public function isValidLink($link)
    {
        $isValid = (
            strpos($link, 'javascript:') !== 0 &&
            strpos($link, '#') !== 0
        );

        if ($isValid) {
            $invalidExts = [
                'jpg', 'jpeg', 'png', 'mp4', 'mpg', 'mp3', '7z', 'zip',
                'msi', 'exe', 'arj', 'ace', 'tar', 'gz', 'iso', 'img', 'dmg',
                'gif', 'xml', 'tif', 'bmp', 'mdb', 'sql', 'dat', 'sqlite',
                'pub', 'doc', 'docx', 'xls', 'xlsx', 'mdbx', 'log', 'txt', 'md',
                'pdf', 'asc', 'ascii', 'gpx', 'gml', 'rom', 'ico', 'raw', 'ai', 'psd',
                'eps', 'vod', 'lnk', 'webloc', 'odf', 'obj', 'class', 'dll', 'jar', 'war',
                'ps', 'pnp', 'ppt', 'pptx', 'js', 'javascript', 'au3', 'bat', 'vox', 'voc',
                'ram', 'm3u', 'asx', 'avi', 'fla', 'm4v', 'ogg'
            ];
            $isValid = (!in_array(strtolower(Utils::getLinkExt($link)), $invalidExts));

        }
        return $isValid;

    }
}
