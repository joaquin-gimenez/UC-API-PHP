<?php

namespace App\Http\Controllers;

use App\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{

    public function getPlaceByType($cityId,$placeId,$type){
        return response()->json(Media::where('type',$type)
            ->where('cityid',$cityId)
            ->where('placeid',$placeId)
            ->get());
    }

    public function getCityByType($cityId,$type){
        return response()->json(Media::where('type',$type)
            ->where('cityid',$cityId)
            ->get());


    }

}