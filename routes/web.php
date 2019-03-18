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
    
    $router->get('cities',  ['uses' => 'CityController@getAllCities']);  
    
    $router->get('cities/{id}', ['uses' => 'CityController@getCityDetails']);
    
    $router->get('cities/{id}/places', ['uses'=> 'PlaceController@getPlacesByCity']);
    
    $router->get('cities/{cityId}/places/{id}', ['uses'=> 'PlaceController@getPlace']);
    
    $router->get('cities/{cityId}/places/{id}', ['uses'=> 'PlaceController@getPlace']);
    
    $router->get('cities/{cityId}/places/{placeId}/media/{type}', ['uses'=> 'MediaController@getMediaByType']);

    $router->get('cities/search/{search}',['uses'=> 'CityController@search']);

    $router->get('places/getAllPlaces',  ['uses' => 'PlaceController@getAllPlaces']);
    $router->get('cities/getPlaceDetails/{id}', ['uses' => 'PlaceController@getPlaceDetails']);

    // $router->post('city', ['uses' => 'CityController@create']);
    // $router->delete('city/{id}', ['uses' => 'CityController@delete']);
    // $router->put('city/{id}', ['uses' => 'CityController@update']);
    //In Process

    

  });
