<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Team;
use Faker\Generator as Faker;

$factory->define(Team::class, function (Faker $faker) {

    if (count( \App\Sport::all() ) > 0) {
        $sport_id = \App\Sport::first()->id;
    } else {
        $sport_id = factory(\App\Sport::class)->create()->id;
    }

    return [
        'title' => $faker->sentence,
        'is_woman_team' => 0,
        'sport_id' => $sport_id,
    ];
});
