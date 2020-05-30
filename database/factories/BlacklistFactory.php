<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Blacklist;
use Faker\Generator as Faker;

$factory->define(Blacklist::class, function (Faker $faker) {
    return [
        'host' => $faker->word . ".com"
    ];
});
