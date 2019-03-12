<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->get('cities/getAllCities',  ['uses' => 'CityController@getAllCities']);  
    $router->get('cities/getCityDetails/{id}', ['uses' => 'CityController@getCityDetails']);
    $router->post('city', ['uses' => 'CityController@create']);
    $router->delete('city/{id}', ['uses' => 'CityController@delete']);
    $router->put('city/{id}', ['uses' => 'CityController@update']);

    $router->get('places/getAllPlaces',  ['uses' => 'PlaceController@getAllPlaces']);
    $router->get('places/getPlaceDetails/{id}', ['uses' => 'PlaceController@getPlaceDetails']);
  });
