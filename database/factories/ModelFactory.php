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
        'summary' => join(" ", $faker->sentences(rand(1, 3))),
        'image' => $faker->imageUrl,
        'language' => $faker->languageCode,
        'is_private' => $faker->boolean,
        'is_outstanding' => $faker->boolean,
        'country' => $faker->countryCode,
        'place_name' => $faker->city,
        'place_id' => $faker->uuid,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'address' => $faker->address,
        'start' => $faker->dateTimeThisMonth()->format('Y-m-d H:i:s') ,
        'end' => $faker->dateTimeThisMonth()->format('Y-m-d H:i:s') ,
        'type_id' => function () {
            return factory(App\Type::class)->create()->id;
        }
    ];
});

$factory->define(\App\Tag::class, function($faker){
    
    $title = $faker->sentence(rand(3,10));

    return[
        'title' => substr($title, 0, strlen($title) - 1),
        'description' => $faker->text
    ];
});

$factory->define(\App\Type::class, function($faker){
    
    $title = $faker->sentence(rand(3,10));

    return[
        'title' => substr($title, 0, strlen($title) - 1),
        'description' => $faker->text
    ];
});

$factory->define(\App\Rating::class, function ($faker) { 
    return [
        'value' => rand(1, 5)
    ];
});
