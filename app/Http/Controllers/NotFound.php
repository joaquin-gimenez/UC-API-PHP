<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class NotFoundController extends Controller
{

    public function pageNotFound()
    {
      return response()->json([
        "error" => [
            "statusCode" => 404,
            "name" => "Error",
            "message" => "Shared class  \"account\" has no method handling POST /updates"
        ]
        ], 404);
    }
}