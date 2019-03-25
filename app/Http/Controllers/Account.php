<?php

namespace App\Http\Controllers;

use App\Account;
use App\Token;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Carbon\Carbon;



class AccountController extends Controller
{
    private $ttl = 604800;

    private function invalidVersion() {
        return response()->json([
            "error" => [
                "name" => "Error", 
                "message" => "You must supply a valid api version", 
                "errorCode" => "INVALID_API_VERSION", 
                "statusCode" => 400
            ]
            ], 400);
    }

    public function login(Request $request, $apiVersion) {
        if($apiVersion != "v2") {
            return $this->invalidVersion();
        }
        if( empty($request->email) || empty($request->password) ){
            return response()->json([
                "error" => [
                    "statusCode" => 400,
                    "name" => "Error",
                    "message" => "Insufficient parameters supplied.",
                    "errorCode" => "INSUFFICIENT_PARAMETERS_SUPPLIED"
                ]
            ], 400);
        }
        try {
            $account = Account::where("email", $request->email)->firstOrFail();
            if( app('hash')->check($request->input("password"), $account->value('password'), ['rounds' => 10]) ){
                return response()->json([ 
                    "result" => [
                        "status" => "ok", 
                        "token" => app('hash')->make($account->value('userId')), 
                        "expires_in_seconds" => $this->ttl
                    ] 
                    ], 200);
            } else {
                return response()->json([ 
                    "token" => [
                        "status" => "error", 
                        "message" => "Incorrect Password" 
                    ]
                    ], 400);
            }
        } catch(\Exception $exception) {
            return response()->json([ 
                "token" => [
                    "status" => "error", 
                    "message" => "email not found" 
                ]
            ], 400);
        }
    }
    
    public function register(Request $request, $apiVersion) {
        if( $apiVersion != "v2" ) {
            return $this->invalidVersion();
        }
        if( $request->email && $request->password && $request->full_name) {
            try {
                if(Account::where("email", $request->email)->count() == 0) {
                    $userId = Uuid::uuid1()->toString();  
                    $request->request->add([ "userid" => $userId ]);
                    $request["password"] = app('hash')->make($request->input("password"), ['rounds' => 10]);
                    $account = Account::create($request->all());
                    $newToken = app('hash')->make( $userId . "_" . date("Y-m-j G:i:s") );
                    $token = Token::create([ "token" => $newToken, "userid" => $userId ] )->first();
                    // TODO sendTokenToGateway
                    return response()->json([ 
                        "token" => [
                            "status" => "ok",
                            "token" => $newToken,
                            "expires_in_seconds" => $this->ttl
                        ]
                    ], 200);
                } else {
                    return response()->json([ 
                        "token" => [ 
                            "status" => "error", 
                            "message" => "Email already exists"
                        ]
                    ], 200);
                }
            } catch(\Exception $exception) {
                return response()->json([
                    "error" => [
                        "message" => 'Something went wrong. Write to us if this persists',
                        "errorCode" => 'OTHER_ERROR',
                        'status' => 500
                    ]
                ], 500);
            }
        } else {
            return response()->json([
                "error" => [
                    "statusCode" => 400,
                    "name" => "Error",
                    "message" => "Insufficient parameters supplied.",
                    "errorCode" => "INSUFFICIENT_PARAMETERS_SUPPLIED"
                ]
            ],400);
        }
    }

    public function getProfile(Request $request, $apiVersion)
    {
        if($apiVersion != "v2"){
            return $this->invalidVersion();
        }
        if($request->header('authorization')) {
            $verifiedToken = $this->verifyToken($request->header('authorization'));
            if(gettype($verifiedToken) == 'array' && isset($verifiedToken['error'])){
                return response()->json($verifiedToken, 401);
            } else {
                try {
                    $account = Account::where('userid', $verifiedToken->value('userid'))->firstOrFail();
                    return response()->json([
                        "result" => [
                            'userid' => $account->value('userid'),
                            'email' => $account->value('email'),
                            'full_name' => $account->value('full_name')
                        ]
                    ]);
                } catch (\Exception $exception){
                    return response()->json([
                        "error" => [
                            "message" => 'Something went wrong and we couldn\'t fetch the profile. Write to us if this persists',
                            "errorCode" => 'OTHER_ERROR',
                            'status' => 500
                        ]
                    ], 500);
                }
            }
        } else {
            return response()->json([
                "error" => [
                    "message" => 'Authorization Required',
                    "errorCode" => 'AUTH_REQUIRED',
                    "status" => 401
                ]
            ], 401);
        }
    }

    public function updateProfile(Request $request, $apiVersion)
    {
        if($apiVersion != "v2"){
            return $this->invalidVersion();
        }
        if($request->header('authorization')) {
            if( empty($request->email) && empty($request->full_name) && empty($request->password) ) {
                return response()->json([
                    "error" => [
                        "statusCode" => 400,
                        "name" => "Error",
                        "message" => "Insufficient parameters supplied.",
                        "errorCode" => "INSUFFICIENT_PARAMETERS_SUPPLIED"
                    ]
                ], 400);
            } else {
                $verifiedToken = $this->verifyToken($request->header('authorization'));
                if(gettype($verifiedToken) == 'array' && isset($verifiedToken['error'])){
                    return response()->json($verifiedToken, 401);
                } else {
                    try {
                        $valuesToUpdate = [];
                        if(isset($request->email)) { $valuesToUpdate["email"] = $request->email; }
                        if(isset($request->full_name)) { $valuesToUpdate["full_name"] = $request->full_name ; }
                        if(isset($request->password)) { $valuesToUpdate["password"] = app('hash')->make($request->password, ['rounds' => 10]); }
                        $account = Account::where('userid', $verifiedToken->value('userid'))->firstOrFail();
                        $account->update( $valuesToUpdate );
                        return response()->json([
                            "result" => [
                                "status" => "ok",
                                "updated_profile" => $valuesToUpdate
                            ]
                        ]);
                    } catch (\Exception $exception){
                        return response()->json([
                            "error" => [
                                "message" => 'Something went wrong and we couldn\'t fetch the profile. Write to us if this persists',
                                "errorCode" => 'OTHER_ERROR',
                                'status' => 500
                            ]
                        ], 500);
                    }
                }
            }
        } else {
            return response()->json([
                "error" => [
                    "message" => 'Authorization Required',
                    "errorCode" => 'AUTH_REQUIRED',
                    "status" => 401
                ]
            ], 401);
        }
    }

    private function verifyToken($sentToken) {
        try {
            $token = Token::where('token', $sentToken)->firstOrFail();
            $date = $token->value('createdate');
            if($date->diffInSeconds() <= $this->ttl) {
                return $token;
            } else {
                return response()->json([
                    "error" => [
                        "message" => 'Token Expired',
                        "errorCode" => 'AUTH_EXPIRED',
                        "status" => 403
                    ]
                ], 403);
            }
        } catch(\Exception $exception) {
            return response()->json([
                "error" => [
                    "message" => 'Incorrect Authentication',
                    "errorCode" => 'AUTH_INVALID',
                    "statusCode" => 403
                ]
            ], 403);
        }
    }
}