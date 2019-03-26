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



$router->get('{apiVersion}/cities',  ['uses' => 'CityController@getAllCities']);
$router->get('{apiVersion}/cities/search',['uses' => 'CityController@search']); 
$router->get('{apiVersion}/cities/{id:[0-9]+}', ['uses' => 'CityController@getCityDetails']);
$router->get('{apiVersion}/cities/current',['uses' => 'CityController@getCurrentCity']); 
$router->get('{apiVersion}/cities/{id:[0-9]+}/places', ['uses'=> 'PlaceController@getPlacesOfCity']);
$router->get('{apiVersion}/cities/{cityId:[0-9]+}/places/{id:[0-9]+}', ['uses'=> 'PlaceController@getPlaceDetails']);
$router->get('{apiVersion}/cities/{cityId:[0-9]+}/media[/{type}]', ['uses' => 'MediaController@getAllMediaByCityId']);
$router->get('{apiVersion}/cities/{cityId:[0-9]+}/places/{placeId:[0-9]+}/media/{type}', ['uses' => 'MediaController@getAllMediaByPlaceId']);

$router->get('{apiVersion}/account', ['uses' => 'AccountController@getProfile']);
$router->put('{apiVersion}/account', ['uses' => 'AccountController@register']);
$router->post('{apiVersion}/account', ['uses' => 'AccountController@login']);
$router->post('{apiVersion}/account/update', ['uses' => 'AccountController@updateProfile']);

$defaultRoute = '/{route:.*}/';
$router->get($defaultRoute, ['uses' => 'NotFoundController@pageNotFound']);
$router->post($defaultRoute, ['uses' => 'NotFoundController@pageNotFound']);
$router->put($defaultRoute, ['uses' => 'NotFoundController@pageNotFound']);
$router->delete($defaultRoute, ['uses' => 'NotFoundController@pageNotFound']);
$router->patch($defaultRoute, ['uses' => 'NotFoundController@pageNotFound']);

