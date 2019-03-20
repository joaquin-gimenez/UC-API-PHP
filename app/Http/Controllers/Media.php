<?php

namespace App\Http\Controllers;

use App\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    
    // --------------------Get All Media of a Place of the specifiecd Type -----------------------
    
    public function getPlaceByType($cityId,$placeId,$type) {
        try {
            return response()->json(Media::where('type',$type)->where('cityid',$cityId)->where('placeid',$placeId)->get());;
            
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
    public function getCityByType($cityId,$type) {
        try {
            return response()->json(Media::where('type',$type)->where('cityid',$cityId)->get());
                    
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