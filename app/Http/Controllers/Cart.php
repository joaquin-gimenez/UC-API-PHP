<?php

namespace App\Http\Controllers;

use App\Media;
use Illuminate\Http\Request;
use App\Token;
use Carbon\Carbon;

class CartController extends Controller {


    private function verifyToken($sentToken) {
        try {
            $token = Token::where('token', $sentToken)->firstOrFail();
            $date = $token->createdate;
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

    public function getCart(Request $request, $apiVersion) {
        $verifiedToken = $this->verifyToken($request->header('authorization'));

        if( empty($this->verifyToken($request->header->authorization)) ){
            return 'empty';
        } else {
            return 'not empty';
        }

    
        // echo('Token = '.$verifiedToken);
        // if($apiVersion != "v2") {
        //     return $this->invalidVersion();
        // }
        // die($verifiedToken);
        // if( gettype($verifiedToken) == 'array' && isset($verifiedToken['error']) ) {

        //     return response()->json($verifiedToken, 401);

        // } else {

        //     try {
        //     return('test');
            
        //     } catch(\Exception $exception) {
        //         return 'Error';
        //     }
        // }
        
    }

}