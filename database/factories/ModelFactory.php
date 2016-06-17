<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'type' => rand(0,1)
    ];
});

$factory->define(App\Event::class, function ($faker) {
    return [
        'title' => join(" ", $faker->sentences(rand(1, 1))),
        'description' => join(" ", $faker->sentences(rand(7, 9))),
        'summary' => join(" ", $faker->sentences(rand(1, 3)))
    ];
});

$factory->define(\App\Tag::class, function($faker){
    
    $title = $faker->sentence(rand(3,10));

    return[
        'title' => substr($title, 0, strlen($title) - 1),
        'description' => $faker->text
    ];
});
