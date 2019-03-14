<?php

namespace App\Http\Controllers;

use App\Account;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    public function login()
    {
        return response()->json([ "cities" => City::all() ]);
    }
    
    public function register(Request $request)
    {
        if(Account::where("email", $request->email)->count() == 0){
          $request->request->add([ "userid" => Uuid::uuid1()->toString() ]);
          $account = Account::create($request->all());
          return response()->json([ "result" => $account ], 200);
        }else {
          return response()->json([ "token" => [ 
                "status" => "error", 
                "message" => "Email already exists"
              ]
            ], 200);
        }
    }
}