<?php

namespace App\Http\Controllers;

use App\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller {   
    
    // --------------------Get All Places -----------------------

    function getAllPlaces(){
        try{
            return response()->Place::al()->get();

        }catch(\Exception $exception){
            return response()->json([
                'error' => [
                    'statusCode' => 500
                    ,'name' => 'Error'
                    ,'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists'
                    ]
                ],500);
        }
    }

    // --------------------Get All Places of a City -----------------------
    function getPlacesByCity($id) {
        
        try{
            return response()->json( Place::where("cityid",$id)->original->get() );

            }catch(\Exception $exception) {
                return response()->json([
                    'error' => [
                        'statusCode' => 500
                        ,'name' => 'Error'
                        ,'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists'
                        ]
                    ],500);
                    
                }
            }

    // --------------------Get a place from a city -----------------------

    //cityId is not required and the place id is unique therefore cityid is not used
    function getPlace($id) {
                
        try {

            return response()->json( Place::find($id));

        }catch(\Exception $exception) {
            
            return response()->json([
                'error' => [
                    'statusCode' => 500
                    ,'name' => 'Error'
                    ,'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists'
                    ]
                ],500);
        }                
    }
}