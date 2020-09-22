<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Player;
use Faker\Generator as Faker;

$factory->define(Player::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
	    'position' => $faker->randomElement(Player::POSITIONS),
        'team_id' => $faker->randomDigitNot(0),
        'sofifa' => 'https://sofifa.com/player/231747/kylian-mbappe/200056/',
        'transfermarkt' => 'https://www.transfermarkt.es/kylian-mbappe/profil/spieler/342229'
    ];
});