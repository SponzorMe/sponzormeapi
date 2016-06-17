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