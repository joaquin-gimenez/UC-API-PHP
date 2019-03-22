<?php

namespace App\Http\Controllers;

use App\Order;
use App\Token;
use App\Account;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
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

    public function getAllOrders(Request $request, $apiVersion)
    {
        if($apiVersion != "v2") {
            return $this->invalidVersion();
        }

        if($request->header('authorization')) {
            $verifiedToken = $this->verifyToken($request->header('authorization'));
            if(gettype($verifiedToken) == 'array' && isset($verifiedToken['error'])){
                return response()->json($verifiedToken, 401);
            }else{
              $account = Account::where('userid', $verifiedToken->value('userid'))->firstOrFail();
              return response()->json( Order::where('userid', $verifiedToken->value('userid'))->get() );
            }
        }else{
          return response()->json([
              "error" => [
                  "message" => 'Authorization Required',
                  "errorCode" => 'AUTH_REQUIRED',
                  "status" => 401
              ]
          ], 401);
        }

    }

    public function deleteOrder(Request $request, $apiVersion, $id)
    {
        if($apiVersion != "v2") {
            return $this->invalidVersion();
        }


        if($request->header('authorization')) {
            $verifiedToken = $this->verifyToken($request->header('authorization'));
            if(gettype($verifiedToken) == 'array' && isset($verifiedToken['error'])){
                return response()->json($verifiedToken, 401);
            }else{
              $order = Order::findOrFail($id);
              if($order->userid == $verifiedToken->userid){
                $order->delete();
                return response('Deleted Successfully', 200);
              }else{
                return response()->json([
                    "error" => [
                        "message" => 'Incorrect Authentication',
                        "errorCode" => 'AUTH_INVALID',
                        "statusCode" => 403
                    ]
                ], 403);
              }
            }
        }else{
          return response()->json([
              "error" => [
                  "message" => 'Authorization Required',
                  "errorCode" => 'AUTH_REQUIRED',
                  "status" => 401
              ]
          ], 401);
        }
    }

    private function verifyToken($sentToken)
    {
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
