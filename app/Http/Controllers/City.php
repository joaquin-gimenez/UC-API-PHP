<?php

namespace App\Http\Controllers;

use App\City;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CityController extends Controller
{

    //1
    public function getAllCities()
    {
        try{
            return response()->json([ City::all() ]);
        }
        catch(ModelNotFoundException $exception){
            return response()->json(['message' => 'Error',
            "errorCode" => 'OTHER ERROR',
            'status' => 500]
            ,500);


        }
    }
    //2
    public function getCityDetails($id)
    {    
        $city = City::find($id);
        $city["places"] = $city->places; 
        return response()->json( $city );
    }
    //8
    function search(Request $request){
            $search = $request->search;
            $page = $request->page; 
            $results =response()->json([
                City::where('name','like', "%{$search}%"    )->orwhere('description','like',"%{$search}%")->get()
            ]);
            return [
                'currentPage' => $page,
                'nextPage' => $page+1,
                'count' => count($results->original[0]),
                'results' => $results->original[0]
            ];
    }
    public function getCityByName(Request $request){
        $name = $request->name;
        return response()->json( City::where("name",$name)->get());
    }

    // public function create(Request $request)
    // {
    //     $city = City::create($request->all());

    //     return response()->json($city, 201);
    // }

    // public function update($id, Request $request)
    // {
    //     $city = City::findOrFail($id);
    //     $city->update($request->all());

    //     return response()->json($city, 200);
    // }

    // public function delete($id)
    // {
    //     City::findOrFail($id)->delete();
    //     return response('Deleted Successfully', 200);
    // }
}