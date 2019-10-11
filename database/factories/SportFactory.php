<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Sport;
use Faker\Generator as Faker;

$factory->define(Sport::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
    ];
});
