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
    public function getAllMediaByPlaceId($cityId, $placeId, $type, $apiVersion) {
        if( $apiVersion != "v2" ) {
            return $this->invalidVersion();
        }
        if( empty($cityId) || empty($placeId) ) {
            return response()->json([
                'error' => [
                    'statusCode' => 404
                    ,'errorCode' => "OTHER_ERROR"
                    ,'message' => 'Insufficient paramters supplied. You must supply a city id and a place id'
                ]
            ],404);
        }
        try {
            $result = response()->json(Media::where('type',$type)->where('cityid', $cityId)->where('placeid', $placeId)->get());

            if( count($result->original) == 0 ) {
                return response()->json([
                    'error' => [
                        'statusCode' => 404
                        ,'errorCode' => "OTHER_ERROR"
                        ,'message' => 'Didn\'t find anything with the given criterion'
                    ]
                ],404);
            }

            return response()->json([Media::where('type',$type)->where('cityid', $cityId)->where('placeid', $placeId)->get()]);
            
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
    public function getAllMediaByCityId($cityId, $type = null, $apiVersion) {
        if( $apiVersion != "v2" ) {
            return $this->invalidVersion();
        }
        try {
            if(!$type) {
                $result = response()->json(Media::where('cityid', $cityId)->get(),200);
            } else {
                $result = response()->json(Media::where('type', $type)->where('cityid', $cityId)->get(),200);
            }


            if( count($result->original ) == 0) {
                return response()->json([
                    'error' => [
                        'statusCode' => 404
                        ,'errorCode' => "OTHER_ERROR"
                        ,'message' => 'Didn\'t find anything with the given criterion'
                    ]
                ],404);
            }

            return $result;
            
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