<?php

namespace App\Http\Controllers;

use App\Account;
use App\Token;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;



class AccountController extends Controller
{
    private $ttl = 604800;

    public function login(Request $request)
    {
        $account = Account::where("email", $request->email);
        if($account->count() == 1){
            if(app('hash')->check($request->input("password"), $account->value('password'), ['rounds' => 10])){
                return response()->json([ 
                    "status" => "ok", 
                    "token" => app('hash')->make($account->value('userId')), 
                    "expires_in_seconds" => $this->$ttl 
                ]);
            }else{
                return response()->json([ "status" => "error", "message" => "Incorrect Password" ]);
            }
        }else {
            return response()->json([ "status" => "error", "message" => "email not found" ]);
        }
    }
    
    public function register(Request $request)
    {
        if($request->email && $request->password && $request->full_name){
            if(Account::where("email", $request->email)->count() == 0){
            $userId = Uuid::uuid1()->toString();  
            $request->request->add([ "userid" => $userId ]);
            $request["password"] = app('hash')->make($request->input("password"), ['rounds' => 10]);
            $account = Account::create($request->all());
            $newToken = app('hash')->make( $userId . "_" . date("Y-m-j G:i:s") );
            $token = Token::create([ "token" => $newToken, "userid" => $userId ] )->first();
            return response()->json([ 
                "token" => [
                    "status" => "ok",
                    "token" => $newToken,
                    "expires_in_seconds" => $this->$ttl
                ]
            ], 200);
            }else {
            return response()->json([ "token" => [ 
                    "status" => "error", 
                    "message" => "Email already exists"
                ]
                ], 200);
            }
        }else{
            return response()->json([
                "error" => [
                    "name" => "Error",
                    "message" => "Insufficient parameters supplied.",
                    "errorCode" => "INSUFFICIENT_PARAMETERS_SUPPLIED"
                ]
            ],400);
        }
    }
}