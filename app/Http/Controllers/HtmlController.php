<?php

namespace App\Http\Controllers;

use App\Services\Providers\HtmlProvider;
use App\Services\Responses\ApiResponse;
use Illuminate\Http\Response;

class HtmlController extends Controller
{

    public function pop()
    {
        $html = app()->make(HtmlProvider::class)->popHtmlForProcessing();
        if ($html === null) {
            return (new ApiResponse(Response::HTTP_NO_CONTENT));

        } else {
            return (new ApiResponse(Response::HTTP_OK))
                ->getResponse($html);
        }
    }

    public function markProcessed($htmlId)
    {
        $html = app()->make(HtmlProvider::class)->markHtmlAsProcessed($htmlId);
        if ($html === null) {
            return (new ApiResponse(Response::HTTP_NOT_FOUND))
                ->getResponse('entity not found');
        } else {
            return (new ApiResponse(Response::HTTP_OK))
                ->getResponse($html);
        }
    }
}
