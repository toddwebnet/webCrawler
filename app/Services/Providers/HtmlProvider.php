<?php

namespace App\Services\Providers;

use App\Jobs\HtmlJob;
use App\Models\Html;
use App\Models\Link;
use App\Models\QueueHtml;
use App\Services\QueueService;

class HtmlProvider
{
    /**
     * @param $urlId
     * @param $html
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function addToQueue($urlId, $html)
    {
        $this->invalidateOldItems($urlId);
        app()->make(QueueService::class)->sendToQueue(HtmlJob::class, [
            'htmlId' => $this->getObj($urlId, $html)->id
        ], 'htmls');

    }

    private function invalidateOldItems($urlId)
    {
        Html::where('url_id', $urlId)->update(['is_valid' => false]);
        Link::where('url_id', $urlId)->update(['is_valid' => false]);
    }

    private function getObj($urlId, $html)
    {
        return Html::create([
            'url_id' => $urlId,
            'html' => $html
        ]);
    }

    public function popHtmlForProcessing()
    {
        $html = Html::where('process_status', HTML::UNPROCESSED)->first();
        if ($html !== null) {
            $html->process_status = HTML::FLAGGED;
            $html->save();
        }
        return $html;
    }

    public function markHtmlAsProcessed($htmlId)
    {
        $html = Html::find($htmlId);
        if ($html !== null) {
            $html->process_status = HTML::PROCESSED;
            $html->save();
        }
        return $html;
    }
}
