<?php

namespace App\Http\Controllers;

use App\Order;
use App\Token;
use App\City;
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
          try{
            $verifiedToken = $this->verifyToken($request->header('authorization'));
            if(gettype($verifiedToken) == 'array' && isset($verifiedToken['error'])){
                return response()->json($verifiedToken, 401);
            }else{
              return response()->json( $this->returnOrdersByUserId($verifiedToken->value('userid')) );
            }
          } catch(\Exception $exception) {
              return response()->json([
                  "error" => [
                      "message" => 'Something went wrong and we couldn\'t get the items of the order. Write to us if this persists',
                      "errorCode" => 'OTHER_ERROR',
                      'status' => 500
                  ]
              ], 500);
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
          try{
            $verifiedToken = $this->verifyToken($request->header('authorization'));
            if(gettype($verifiedToken) == 'array' && isset($verifiedToken['error'])){
                return response()->json($verifiedToken, 401);
            }else{
              $order = Order::findOrFail($id);
              if($order->userid == $verifiedToken->userid){
                $order->delete();
                return response()->json( $this->returnOrdersByUserId($verifiedToken->value('userid')) );
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
          } catch(\Exception $exception) {
              return response()->json([
                  "error" => [
                      "message" => 'Something went wrong and we couldn\'t delete the order. Write to us if this persists',
                      "errorCode" => 'OTHER_ERROR',
                      'status' => 500
                  ]
              ], 500);
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

    private function returnOrdersByUserId($userid)
    {
      $orders = Order::where('userid', $userid)->get();

      foreach ($orders as $order) {
        $city = City::findOrFail($order->cityid);
        $order->cityname = $city->name;
        $order->description = $city->description;
      }

      return $orders;
    }

}
