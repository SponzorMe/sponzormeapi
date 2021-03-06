<?php


/*
--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
	return $app->version();
}
);


/** @users **/
$app->get('/users', 'UsersController@index');
$app->get('/users/{id:[\d]+}', [
    'as'    => 'users.show',
    'uses'  => 'UsersController@show'
]);
$app->post('/users', 'UsersController@store');
$app->put('/users/{id:[\d]+}', 'UsersController@update');
$app->delete('/users/{id:[\d]+}', 'UsersController@destroy');



/** @events **/
$app->get('/events', 'EventsController@index');
$app->get('/events/{id:[\d]+}', [
    'as'    => 'events.show',
    'uses'  => 'EventsController@show'
]);
$app->post('/events', 'EventsController@store');
$app->put('/events/{id:[\d]+}', 'EventsController@update');
$app->delete('/events/{id:[\d]+}', 'EventsController@destroy');

/** @sponsorship_types **/
$app->get('/sponsorship_types', 'SponsorshipTypesController@index');
$app->get('/sponsorship_types/{id:[\d]+}', [
    'as'    => 'sponsorship_types.show',
    'uses'  => 'SponsorshipTypesController@show'
]);
$app->post('/sponsorship_types', 'SponsorshipTypesController@store');
$app->put('/sponsorship_types/{id:[\d]+}', 'SponsorshipTypesController@update');
$app->delete('/sponsorship_types/{id:[\d]+}', 'SponsorshipTypesController@destroy');

/** @perks **/
$app->get('/perks', 'PerksController@index');
$app->get('/perks/{id:[\d]+}', [
    'as'    => 'perks.show',
    'uses'  => 'PerksController@show'
]);
$app->post('/perks', 'PerksController@store');
$app->put('/perks/{id:[\d]+}', 'PerksController@update');
$app->delete('/perks/{id:[\d]+}', 'PerksController@destroy');

/** @sponsorships **/
$app->get('/sponsorships', 'SponsorshipsController@index');
$app->get('/sponsorships/{id:[\d]+}', [
    'as'    => 'sponsorships.show',
    'uses'  => 'SponsorshipsController@show'
]);
$app->post('/sponsorships', 'SponsorshipsController@store');
$app->put('/sponsorships/{id:[\d]+}', 'SponsorshipsController@update');
$app->delete('/sponsorships/{id:[\d]+}', 'SponsorshipsController@destroy');

/** @tasks **/
$app->get('/tasks', 'TasksController@index');
$app->get('/tasks/{id:[\d]+}', [
    'as'    => 'tasks.show',
    'uses'  => 'TasksController@show'
]);
$app->post('/tasks', 'TasksController@store');
$app->put('/tasks/{id:[\d]+}', 'TasksController@update');
$app->delete('/tasks/{id:[\d]+}', 'TasksController@destroy');

/** @tags **/
$app->group([
    'prefix' => '/tags',
    'namespace' => 'App\Http\Controllers'
], function (\Laravel\Lumen\Application $app) {
        $app->get('/{id:[\d]+}', [
                'as' => 'tags.show',
                'uses' => 'TagsController@show'
            ]
        );

        $app->put('/{tagId:[\d]+}/events/{eventId:[\d]+}',
                'TagsController@addEvent'
        );
        
        $app->delete(
                '/{tagId:[\d]+}/events/{eventId:[\d]+}',
                'TagsController@removeEvent'
        );
    }
);

// User ratings
$app->post('/users/{id:[\d]+}/ratings', 'UsersRatingsController@store');
$app->delete('/users/{userId:[\d]+}/ratings/{ratingId:[\d]+}', 'UsersRatingsController@destroy');