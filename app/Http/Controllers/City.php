<?php

namespace App\Http\Controllers;

use App\City;
use Illuminate\Http\Request;

class CityController extends Controller {
    
    // ---------------- Get All City ----------------
    public function getAllCities() {
        try {
            
            return response()->json([ City::all() ]);
            
        }catch(\Exception  $exception) {
            
            return response()->json([
                'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists',
                "errorCode" => 'OTHER ERROR',
                'status' => 500]
                ,500);
            }
        }
        
        // ---------------- Get City Details ----------------
        
        public function getCityDetails($id) {    
            
            try {
                $city = City::findOrFail($id);
                $city["places"] = $city->places; 
                
                return response()->json( $city );
                
            }catch(\Exception $exception) {
                
                return response()->json([
                    'message' => 'Something went wrong and we couldn\'t fulfil this request. Write to us if this persists',
                    'error' => 'OTHER ERORR',
                    'status' => 500],
                    500);
                }
            }
            
            
            // ---------------- Search ----------------    
            
            public function search(Request $request) {
                
                $page = $request->page; 
                $search = $request->search;
                
                try {
                    
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
                            'message' => 'Didn\'t find anything with this keyword',
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
                            
                            $results = response()->json(City::where('name',$name)->get());
                            
                            if(count($results->original)==0){
                                return response()->json([
                                    'message' => 'No city found',
                                    'error' => 'OTHER ERORR',
                                    'status' => 404],
                                    404);
                                    
                                }
                                
                                else{
                                    return $results;
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
                        