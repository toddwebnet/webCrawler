<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UrlSizes extends Model
{
    const DAILY_DOWNLOAD_LIMIT = 120000000;//100000000;//100,000,000;


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

    public static function allowDownloads()
    {
        return self::getTodayCount() < env('DAILY_DOWNLOAD_LIMIT', 100000);
    }
}
