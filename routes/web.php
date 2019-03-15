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
    //Done
    //1
    $router->get('cities',  ['uses' => 'CityController@getAllCities']);  
    //2
    $router->get('cities/{id}', ['uses' => 'CityController@getCityDetails']);
    //3
    //4
    $router->get('cities/{id}/places', ['uses'=> 'PlaceController@getPlacesByCity']);
    //5
    $router->get('cities/{cityId}/places/{id}', ['uses'=> 'PlaceController@getPlace']);
    //6
    //create Media controller
    //7
    //????????
    //8
    $router->get('cities/search/{search}',['uses'=> 'CityController@search']);



    // $router->post('city', ['uses' => 'CityController@create']);
    // $router->delete('city/{id}', ['uses' => 'CityController@delete']);
    // $router->put('city/{id}', ['uses' => 'CityController@update']);
    //In Process

    
    $router->get('places/getAllPlaces',  ['uses' => 'PlaceController@getAllPlaces']);
    $router->get('cities/getPlaceDetails/{id}', ['uses' => 'PlaceController@getPlaceDetails']);

    $router->get('account', ['uses' => 'AccountController@getProfile']);
    $router->put('account', ['uses' => 'AccountController@register']);
    $router->post('account', ['uses' => 'AccountController@login']);
    $router->post('account/update', ['uses' => 'AccountController@updateProfile']);
    
  });
