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

    // --------------------Get All Places -----------------------
    function getAllPlaces($apiVersion){
        if($apiVersion != "v2") {
            return $this->invalidVersion();
        }
        try{
            return response()->Place::al()->get();
        }catch(\Exception $exception){
            return response()->json([
                'error' => [
                    'statusCode' => 500
                    ,'errorCode' => "OTHER_ERROR"
                    ,'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists'
                ]
            ],500);
        }
    }

    // --------------------Get All Places of a City -----------------------
    function getPlacesByCity($id, $apiVersion) {
        if($apiVersion != "v2") {
            return $this->invalidVersion();
        }
        try{
            return response()->json( Place::where("cityid", $id)->get() );
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

    // --------------------Get a place from a city -----------------------
    function getPlace($id, $apiVersion) {
                
        if($apiVersion != "v2") {
            return $this->invalidVersion();
        }
        try {
            return response()->json( Place::findOrFail($id));
        
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