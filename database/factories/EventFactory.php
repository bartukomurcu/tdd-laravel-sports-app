<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Event;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {

    return [
        'date_time' => $faker->dateTime,
        'home_team_score' => '0',
        'away_team_score' => '0',
    ];
});
