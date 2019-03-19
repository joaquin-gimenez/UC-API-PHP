<?php

namespace App\Http\Controllers;

use App\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    
    // --------------------Get All Media of a Place of the specifiecd Type -----------------------
    
    public function getPlaceByType($cityId,$placeId,$type) {
        try {
            $result = response()->json(Media::where('type',$type)
            ->where('cityid',$cityId)
            ->where('placeid',$placeId)
            ->get());
            //return
            
            if (count($result->original)>0) {
                return $result;
            }
            else {
                return response()->json([
                    'message' => 'No place matches the search parameter',
                    'error' => 'OTHER ERORR',
                    'status' => 404],
                    404);
                }
            }catch(\Exception $exception) {
                return response()->json([
                    'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists',
                    'error' => 'OTHER ERORR',
                    'status' => 500],
                    500);
                }
            }
            
            
            // --------------------Get All Media of a City of the specifiecd Type -----------------------
            public function getCityByType($cityId,$type) {
                try{
                    $result = response()->json(Media::where('type',$type)
                    ->where('cityid',$cityId)
                    ->get());
                    
                    if (count($result->original)>0) {
                        return $result;
                    }
                    else{
                        return response()->json([
                            'message' => 'No city matches the search parameter',
                            'error' => 'OTHER ERORR',
                            'status' => 404],
                            404);
                        }
                        
                    }catch(\Exception $exception) {
                        return response()->json([
                            'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists',
                            'error' => 'OTHER ERORR',
                            'status' => 500],
                            500);
                        }
                        
                    }
                    
                }