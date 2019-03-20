<?php

namespace App\Http\Controllers;

use App\Token;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    
    // public function getTokenDetails($id)
    // {
    //     $token = Token::find($id);
    //     return response()->json([ "token" => $token ]);
    // }

    public function create($req)
    {
        return Token::create($req);
    }
}