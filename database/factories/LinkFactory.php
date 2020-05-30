<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Link;
use App\Models\Url;
use Faker\Generator as Faker;

$factory->define(Link::class, function (Faker $faker) {
    return [
        'url_id' =>  Url::inRandomOrder()->first()->id,
        'link' => $faker->url,
        'text' => $faker->sentence
    ];
});
