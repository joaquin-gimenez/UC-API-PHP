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
    
    
    $router->get('cities',  ['uses' => 'CityController@getAllCities']);  

    $router->get('cities/search',['uses' => 'CityController@search']);

    $router->get('cities/current',['uses' => 'CityController@getCurrentCity' ]);
    
    $router->get('cities/{id}', ['uses' => 'CityController@getCityDetails']);
    
    $router->get('cities/{id}/places', ['uses' => 'PlaceController@getPlacesByCity']);
      
    $router->get('cities/{cityId}/places/{id}', ['uses' => 'PlaceController@getPlace']);
    
    $router->get('cities/{cityId}/media/{type}', ['uses' => 'MediaController@getCityByType']);

    $router->get('cities/{cityId}/places/{placeId}/media/{type}', ['uses' => 'MediaController@getPlaceByType']);

    
    // $router->post('city', ['uses' => 'CityController@create']);
    // $router->delete('city/{id}', ['uses' => 'CityController@delete']);
    // $router->put('city/{id}', ['uses' => 'CityController@update']);
    //In Process

    

  });
