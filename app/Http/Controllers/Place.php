<?php

namespace App\Http\Controllers;

use App\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller {   
    

    // --------------------Get All Places of a City -----------------------
    function getPlacesByCity($id) {
        
        try{
            $result = response()->json( Place::where("cityid",$id)->get() );
            
            if(count($result->original)>0) {
                return $result;
            }
            return response()->json([
                'message' => 'No city was found with the provided id',
                'error' => 'OTHER ERORR',
                'status' => 404],
                404);
            }catch(\Exception $exception) {
                return response()->json([
                    'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists',
                    'error' => 'OTHER ERORR',
                    'status' => 500],
                    500);
                    
                }
            }

            // --------------------Get a place from a city -----------------------

            //cityId is not required and the place id is unique therefore cityid is not used
            function getPlace($id) {
                
                try {
                    $result = response()->json( Place::find($id));
                    
                    if(count($result->original)>0){
                        
                        return $result;
                        
                    }
                    else {
                        return response()->json([
                            'message' => 'No place with the id provided was found',
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