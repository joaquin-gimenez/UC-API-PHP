<?php

namespace App\Http\Controllers;

use App\City;
use Illuminate\Http\Request;

class CityController extends Controller
{

    //1
    public function getAllCities()
    {
        return response()->json([ City::all() ]);
    }
    //2
    public function getCityDetails($id)
    {    
        $city = City::find($id);
        $city["places"] = $city->places; 
        return response()->json( $city );
    }
    //8
    function search($search){
        return response()->json(City::where('name','like', "%{$search}%")
        ->orwhere('description','like',"%{$search}%") -> get());
    }


    
    // public function getCity (Request $request)
    // {
    //     $this->validate($request, [
    //         'properties'=>'required',
    //         'lat'=>'required',
    //         'lng'=>'required',
    //         'createdate'=>'required',
    //         'lastupdate'=>'required',
    //         'heroimage'=>'required',
    //         'besttime'=>'required',
    //         'language'=>'required',
    //         'population'=>'required',
    //         'currency'=>'required',
    //         'tour_price'=>'required',
    //     ]);

    //     $city =  City::create($request->all());

    //     return response()->json($city,201);

    // }

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