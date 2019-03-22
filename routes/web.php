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
$router->get('{apiVersion}/cities/{id}', ['uses' => 'CityController@getCityDetails']);
$router->get('{apiVersion}/cities/{id}/places', ['uses'=> 'PlaceController@getPlacesByCity']);
$router->get('{apiVersion}/cities/{cityId}/places/{id}', ['uses'=> 'PlaceController@getPlace']);
$router->get('{apiVersion}/cities/search/{search}',['uses'=> 'CityController@search']);
$router->get('{apiVersion}/places/getAllPlaces',  ['uses' => 'PlaceController@getAllPlaces']);
$router->get('{apiVersion}/cities/getPlaceDetails/{id}', ['uses' => 'PlaceController@getPlaceDetails']);

$router->get('{apiVersion}/account', ['uses' => 'AccountController@getProfile']);
$router->put('{apiVersion}/account', ['uses' => 'AccountController@register']);
$router->post('{apiVersion}/account', ['uses' => 'AccountController@login']);
$router->post('{apiVersion}/account/update', ['uses' => 'AccountController@updateProfile']);

$router->get('{apiVersion}/orders', ['uses' => 'OrderController@getAllOrders']);
$router->delete('{apiVersion}/orders/{id}', ['uses' => 'OrderController@deleteOrder']);

$defaultRoute = '/{route:.*}/';
$router->get($defaultRoute, ['uses' => 'NotFoundController@pageNotFound']);
$router->post($defaultRoute, ['uses' => 'NotFoundController@pageNotFound']);
$router->put($defaultRoute, ['uses' => 'NotFoundController@pageNotFound']);
$router->delete($defaultRoute, ['uses' => 'NotFoundController@pageNotFound']);
$router->patch($defaultRoute, ['uses' => 'NotFoundController@pageNotFound']);
