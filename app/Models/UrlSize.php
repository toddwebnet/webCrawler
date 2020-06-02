<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UrlSize extends Model
{

    protected $fillable = [
        'url_id',
        'size',
        'timestamp',
    ];

    public $timestamps = false;

    public static function getTodayCount()
    {
        return self::where('timestamp', '>', strtotime('today 12:00 am'))->sum('size');
    }

    public static function getDailyLimit()
    {
        $limit = env('DAILY_DOWNLOAD_LIMIT', 100000);
        return $limit;
    }

    public static function allowDownloads()
    {
        $limit = self::getDailyLimit();
        $todayCount = self::getTodayCount();
        $allowDownloads = $todayCount < $limit ? true : false;
        return $allowDownloads;
    }
}
