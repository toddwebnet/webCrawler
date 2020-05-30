<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Html;
use App\Models\Url;
use Faker\Generator as Faker;

$factory->define(Html::class, function (Faker $faker) {
    return [
        'url_id' => Url::inRandomOrder()->first()->id,
        'html' => substr(str_replace(' ', '', $faker->sentence), 0, 2000)
    ];
});
