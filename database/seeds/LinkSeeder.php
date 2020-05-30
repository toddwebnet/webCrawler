<?php

use App\Models\Link;
use App\Models\Html;
use App\Models\Url;
use App\Models\UrlOverflow;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Html::all() as $html) {
            for ($x = 0; $x < rand(0, 5); $x++) {
                $link = factory(Link::class)->create([
                    'url_id' => $html->url_id
                ]);
                $parsed = parse_url($link->link);
                $newUrl = factory(Url::class)
                    ->create([
                        'url' => $link->link,
                        'host' => $parsed['host']
                    ]);

                factory(UrlOverflow::class)->create(['url_id' => $newUrl->id]);

            }
        }
    }
}
