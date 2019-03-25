<?php
namespace App\Http\Controllers;
use App\Media;
use Illuminate\Http\Request;

class MediaController extends Controller {

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
    
    // --------------------Get All Media of a Place of the specifiecd Type -----------------------
    public function getPlaceByType($cityId, $placeId, $type, $apiVersion) {
        if($apiVersion != "v2") {
            return $this->invalidVersion();
        }
        
        try {
            return response()->json(Media::where('type',$type)->where('cityid', $cityId)->where('placeid', $placeId)->get());;
            
            }catch(\Exception $exception) {
                return response()->json([
                    'error' => [
                        'statusCode' => 500
                        ,'errorCode' => "OTHER_ERROR"
                        ,'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists'
                        ]
                    ],500);
            }
    }
            
    // --------------------Get All Media of a City of the specifiecd Type -----------------------
    public function getCityByType($cityId, $type, $apiVersion) {
        if($apiVersion != "v2") {
            return $this->invalidVersion();
        }
        try {
            return response()->json(Media::where('type', $type)->where('cityid', $cityId)->get());
                    
        }catch(\Exception $exception) {
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