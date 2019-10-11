<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\League;
use Faker\Generator as Faker;

$factory->define(League::class, function (Faker $faker) {

    if (count( \App\Sport::all() ) > 0) {
        $sport_id = \App\Sport::first()->id;
    } else {
        $sport_id = factory(\App\Sport::class)->create()->id;
    }

    return [
        'title' => $faker->sentence,
        'sport_id' => $sport_id,
        'start_date' => '03/14/2018',
        'end_date' => '03/24/2018',
        'max_team_no' => $faker->numberBetween(0, 256),
    ];
});
