<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlSize extends Model
{

    protected $fillable = [
        'url_id',
        'size',
        'timestamp',
    ];

    public $timestamps = false;

    public static function getTodaySum()
    {
        return self::where('timestamp', '>', strtotime('today 12:00 am'))->sum('size');
    }

    public static function getTodayCount()
    {
        return self::where('timestamp', '>', strtotime('today 12:00 am'))->count();
    }

    public static function getDailyLimit()
    {
        $limit = env('DAILY_DOWNLOAD_LIMIT', 100000);
        return $limit;
    }

    public static function allowDownloads()
    {
        $limit = self::getDailyLimit();
        $todayCount = self::getTodaySum();
        $allowDownloads = $todayCount < $limit ? true : false;
        return $allowDownloads;
    }
}
