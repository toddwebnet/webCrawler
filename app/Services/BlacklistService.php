<?php

namespace App\Services;

use App\Models\Blacklist;
use Illuminate\Support\Facades\DB;

class BlacklistService
{
    public function isInBlacklist($url)
    {
        $parsed = parse_url($url);
        $parsed['host'] = DB::connection()->getPdo()->quote($parsed['host']);

        // you know a better way?
        if (env('DB_CONNECTION') == 'phpunit') {
            $condition = "host = {$parsed['host']} or {$parsed['host']} like '%.' || host";
        } else {
            $condition = "host = {$parsed['host']} or {$parsed['host']} like concat('%.', host)";
        }
        return Blacklist::whereRaw($condition)
            ->count() === 0 ? false : true;
    }
}
