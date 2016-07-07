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
        'type' => rand(1, 6) % 2 === 0 ? 'organizer' : 'sponsor',
        'gender' => rand(1, 6) % 2 === 0 ? 'male' : 'female',
        'language' => $faker->languageCode
    ];
});

$factory->define(App\Event::class, function ($faker) {
    
    $startDate = $faker->dateTimeThisMonth()->format('Y-m-d H:i:s');
    $timezone = $faker->timezone;
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
        'start' =>  $startDate,
        'end' => $faker->dateTimeBetween($startDate, $endDate = '+ 1 days', $timezone)->format('Y-m-d H:i:s') ,
        'timezone' => $timezone,
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

$factory->define(\App\Sponsorship::class, function($faker){
    
    $title = $faker->sentence(rand(3,10));

    return[
        'cause' => substr($title, 0, strlen($title) - 1),
        'status' => rand(1, 6) % 2 === 0 ? 'accepted' : 'pending',
    ];
});

$factory->define(\App\Task::class, function($faker){
    
    $title = $faker->sentence(rand(3,10));

    return[
        'text' => substr($title, 0, strlen($title) - 1),
        'status' => rand(1, 6) % 2 === 0 ? 'completed' : 'pending'
    ];
});

$factory->define(\App\Perk::class, function($faker){
    
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

$factory->define(\App\SponsorshipType::class, function ($faker) { 
    $title = $faker->sentence(rand(1,3));
    return [
        'kind'=> substr($title, 0, strlen($title) - 1),
        'cost'=>$faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 200),
        'total_slots'=> rand(1, 5)
    ];
});
