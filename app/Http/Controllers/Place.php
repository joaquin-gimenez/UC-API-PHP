<?php

namespace App\Http\Controllers;

use App\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    
    public function getAllPlaces()
    {
        return response()->json([ "places" => Place::all() ]);
    }
    
    public function getPlaceDetails($id)
    {
        return response()->json([ "placeDetails" => Place::find($id) ]);
    }
    //4
    function getPlacesByCity($id){
        return response()->json( Place::where("cityid",$id)->get() );   
    }
    //5
    function getPlacesIdByCity($id){
        return response()->json( Place::where("id",$id) ->get()[0] );
    }



    // public function create(Request $request)
    // {
    //     $place = Place::create($request->all());

    //     return response()->json($place, 201);
    // }

    // public function update($id, Request $request)
    // {
    //     $place = Place::findOrFail($id);
    //     $place->update($request->all());

    //     return response()->json($place, 200);
    // }

    // public function delete($id)
    // {
    //   Place::findOrFail($id)->delete();
    //     return response('Deleted Successfully', 200);
    // }
}