<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FlushDuplicates extends Command
{
    protected $signature = 'flushDupes';

    public function handle()
    {
        $sql = "
        SELECT url, count(*) num, min(id) keepId
        FROM `urls`
        group by host, url
        HAVING COUNT(*) > 1
        ";
        foreach(DB::select($sql) as $row){
            $this->line('Dupes for: ' . $row->url);
            $sql = "delete from urls where url = ? and id <> ?";
            $params = [
                $row->url,
                $row->keepId
            ];
            DB::update($sql, $params);
        }
        $sql = "delete from url_overflows where url_id not in (select id from urls)";
        DB::update($sql);
    }
}
