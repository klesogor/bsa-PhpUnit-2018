<?php

use Faker\Generator as Faker;

$factory->define(App\Entity\Lot::class, function (Faker $faker) {
    return [
        'price' => $faker->randomDigit,
    ];
});
