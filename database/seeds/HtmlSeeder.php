<?php

use App\Models\Html;
use App\Models\Url;
use App\Models\UrlSize;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HtmlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Url::whereNull('last_refreshed')->get() as $urlObj) {

            $urlObj->last_refreshed = new Carbon(rand(0, 365) . ' days ago');
            if (rand(0, 5) == 5) {
                $urlObj->is_valid = false;
            } else {
                factory(Html::class)->create(['url_id' => $urlObj->id]);
                factory(UrlSize::class)->create(['url_id' => $urlObj->id]);
            }
            $urlObj->save();
        }
    }
}
