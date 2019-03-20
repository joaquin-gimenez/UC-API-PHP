<?php

namespace App\Http\Controllers;

use App\City;
use Illuminate\Http\Request;

class CityController extends Controller {
    
    // ---------------- Get All Cities ----------------
    public function getAllCities() {
        try {
            return response()->json([ City::all() ]);
            
        }catch(\Exception  $exception) {
            

            return response()->json([
                'error' => [
                    'status' => 500
                    ,'name' => "Error"
                    ,'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists'
                ]
                ],500);

        }
    }
        
    // ---------------- Get City Details ----------------   
        
    public function getCityDetails($id) {
        if(empty($id)){
            return response()->json([
                    "error" => [
                        "statusCode" => 404,
                        "name" => "Error",
                        "message" => "No id was supplied. You must supply a city id"
                        ]
                    
                    ],404);
        }    
                        
        try {
            $city = City::find($id);
            if(count($city) == 0){
                return response()->json([
                        "error" => [
                            "statusCode" => 404,
                            "name" => "Error",
                            "message" => "Didn't find anything with this id"
                        ]
                    ],404);
            }

            $city["places"] = $city->places; 
            return response()->json( $city );
                                        
        }catch(\Exception $exception) {
                                            
            return response()->json([
            [
            "error" => [
                "statusCode" => 500,
                "name" => "Error",
                "message" => "Something went wrong and we couldn't fulfil this request. Write to us if this persists"
                ]
            ]
            ]);
        }
    }
                                            
    // ---------------- Search ----------------    
                                            
    public function search(Request $request) {
                                                
        $page = $request->page; 
        $search = $request->search;
                                                
        try {

        if(empty($search)){
            return response()->json([
                [
                "error" => [
                    "statusCode" => 404,
                    "name" => "Error",
                    "message" => "No keyword was supplied. You must supply a search keyword"
                    ]
                ]
            ]);                                           
        }
                                                        
        $results =response()->json([City::where( 'name','like', "%{$search}%" )->get()]);
                                                        
        if( strlen($search)>0 && count($results->original[0])>0 ) {
                                                        
            return [
                    'currentPage' => $page,
                    'nextPage' => count($results->original[0]) == 0 ? null : $page + 1,
                    'count' => count($results->original[0]),
                    'results' => $results->original[0]
                    ];
        }
        else {
            return response()->json([
                "error" =>[
                    'statusCode' => 404
                    ,'name' => 'Error'
                    ,'message' => 'Didn\'t find anything with this keyword'
                ]
                ],404);
        }
        }catch(\Exception $exception) {
            return response()->json([
                "error" => [
                    'statusCode' => 500
                    ,'name' => 'Error'
                    ,'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists'
                ]
                ],500);
                                                            
        }
    }
                                                    
     // ---------------- Get Current City ----------------  
                                                    
    public function getCurrentCity(Request $request) {
    
        try{
            $name;
                                                            
            if (isset($_SERVER['HTTP_X_AKAMAI_EDGESCAPE'])) {
                $matches = [];
                preg_match_all("/([^,=]+)=([^,=]+)/", $_SERVER['HTTP_X_AKAMAI_EDGESCAPE'], $matches);
                $edgescape = array_combine($matches[1], $matches[2]);
                foreach ($edgescape as $key => $value) {                                                
                    define("EDGESCAPE_" . strtoupper( $key ), $value);
                    if($key=='city'){
                    $name = $value;
                    }
                }
            }

            return response()->json(City::where('name',$name)->get());
                                                                
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
                                                        