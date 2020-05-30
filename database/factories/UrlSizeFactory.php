<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Url;
use App\Models\UrlSize;
use Faker\Generator as Faker;

$factory->define(UrlSize::class, function (Faker $faker) {
    return [
        'url_id' => Url::inRandomOrder()->first()->id,
        'size' => $faker->numberBetween(1, 10000),
        'timestamp' => time()
    ];
});
