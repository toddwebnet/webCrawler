<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Url;
use Faker\Generator as Faker;

$factory->define(Url::class, function (Faker $faker) {
    $url = $faker->url;
    $parsed = parse_url($url);
    return [
        'url' => $url,
        'host' => $parsed['host']
    ];
});
