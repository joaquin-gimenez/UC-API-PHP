<?php

namespace App\Http\Controllers;

use App\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller {   
    
    //--------------------Invalid Version Messafe-----------------
    private function invalidVersion() {
        return response()->json([
            "error" => [
                "name" => "Error", 
                "message" => "You must supply a valid api version", 
                "errorCode" => "INVALID_API_VERSION", 
                "statusCode" => 400
            ]
        ], 400);
    }

    //--------------------Get All Places of a City -----------------------
    function getPlacesOfCity ($id, $apiVersion) {
        if( $apiVersion != "v2" ) {
            return $this->invalidVersion();
        }
        if( empty($id) ) {
            return response()->json([
                'error' => [
                    'statusCode' => 404,
                    'errorCode' => "OTHER_ERROR",
                    'message' => 'No id was supplied. You must supply a city id'
                ]
            ] ,200);
        }
        
        try{

            $result = response()->json( Place::where("cityid", $id)->get() );
            
            if( count( $result->original ) == 0 ){
                return response()->json([
                    'error' => [
                        'statusCode' => 404,
                        'errorCode' => "OTHER_ERROR",
                        'message' => 'Didn\'t find anything with this id'
                    ]
                ]);
            }
            
            return $result;

        } catch(\Exception $exception) {
            return response()->json([
                'error' => [
                    'statusCode' => 500
                    ,'errorCode' => "OTHER_ERROR"
                    ,'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists'
                ]
            ],500);
                    
        }
    }

    //--------------------Get a place from a city -----------------------
    function getPlaceDetails($id, $apiVersion) {
                
        if( $apiVersion != "v2" ) {
            return $this->invalidVersion();
        }
        if(empty($id)){
            return response()->json([
                'error' => [
                    'statusCode' => 404
                    ,'errorCode' => "OTHER_ERROR"
                    ,'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists'
                ]
            ],404);
        }
        try {
            
            $result = response()->json(Place::find($id));

            if( count($result->original) == 0 ) {
                return response()->json([
                    'error' => [
                        'statusCode' => 404
                        ,'errorCode' => "OTHER_ERROR"
                        ,'message' => 'Didn\'t find anything with this id'
                    ]
                ],404);
            }
            return response()->json([Place::find($id)],200);
        
        } catch(\Exception $exception) {
            
            return response()->json([
                'error' => [
                    'statusCode' => 500
                    ,'errorCode' => "OTHER_ERROR"
                    ,'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists'
                ]
            ],500);
        }                
    }
} 