<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Url;
use App\Models\UrlOverflow;
use Faker\Generator as Faker;

$factory->define(UrlOverflow::class, function (Faker $faker) {
    return [
        'url_id' => Url::inRandomOrder()->first()->id
    ];
});
