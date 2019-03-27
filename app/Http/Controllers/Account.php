<?php

namespace App\Http\Controllers;

use App\Account;
use App\Token;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;



class AccountController extends Controller
{
    private $ttl = 604800;
    private $saltRounds = 10;

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

    public function register(Request $request, $apiVersion) {
        if( $apiVersion != "v2" ) {
            echo "<script>console.log('Account : register : error: invalid API version')</script>";
            return $this->invalidVersion();
        }
        if( $request->email && $request->password && $request->full_name) {
            try {
                if(Account::where("email", $request->email)->count() == 0) {
                    $userId = Uuid::uuid1()->toString();  
                    $request->request->add([ "userid" => $userId ]);
                    $request["password"] = app('hash')->make($request->input("password"), ['rounds' => $this->saltRounds]);
                    $account = Account::create($request->all());
                    $newToken = app('hash')->make( $userId . "_" . date("Y-m-j G:i:s") );
                    $token = Token::create([ "token" => $newToken, "userid" => $userId ] )->firstOrFail();
                    // blocking call to send these tokens to APIG
                    // if this fails, we send an error back as response
                    if( app()->environment('demo') ){
                        if(!$this->sendTokenToGateway($newToken)){
                            echo "<script>console.log('Account : registerWithKey : Unable to create key')</script>\n\n";
                            return response()->json([ 
                                "token" => [
                                    "message" => "Unable to create key",
                                    "errorCode" => 'OTHER_ERROR',
                                    "statusCode" => 500
                                ]
                            ], 500);
                        }
                    }
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
                //TODO error body
                echo "<script>console.log('Account : registerWithKey : error: Account.create')</script>";
                return response()->json([
                    "error" => [
                        "message" => 'Something went wrong. Write to us if this persists',
                        "errorCode" => 'OTHER_ERROR',
                        'status' => 500
                    ]
                ], 500);
            }
        } else {
            echo "<script>console.log('Account : register : error: Supplied parameters insufficient. Body: , " . json_encode($request->all()) . "')</script>";
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

    public function login(Request $request, $apiVersion) {
        echo "<script>console.log('Account:Login: Version, Body, " . $apiVersion .", " . json_encode($request->all()) . "');</script>";
        if($apiVersion != "v2") {
            return $this->invalidVersion();
        }
        if( empty($request->email) || empty($request->password) ){
            echo "<script>console.log('Account : login : error: Supplied parameters insufficient. Body: ," . json_encode($request->all()) . "')</script>";
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
            $userid = $account->userid; 
            if( app('hash')->check($request->password, $account->password, ['rounds' => $this->saltRounds]) ){
                $newToken = app('hash')->make( $userid . "_" . date("Y-m-j G:i:s") );
                // First delete any tokens that exist for this userid
                //$userTokens = Token::where('userid', $userid)->get();
                $deleted = Token::where('userid', $userid)->delete();
                echo "<script>console.log('Login : Token : deleted existing: ," . json_encode($deleted) . "')</script>";
                //$userTokens->delete();
                //TODO showing count deleted 
                if($deleted > 0) {
                    $token = Token::create([ "token" => $newToken, "userid" => $userid ] )->firstOrFail();
                    echo "<script>console.log('Login : Token : Create Result: ," . json_encode($token) . "')</script>";
                    // blocking call to send these tokens to APIG
                    // if this fails, we send an error back as response
                    if( app()->environment('demo') ){
                        if(!$this->sendTokenToGateway($newToken)){
                            echo "<script>console.log('Account : login : Unable to update key')</script>\n\n";
                            return response()->json([ 
                                "token" => [
                                    "message" => "Unable to update key",
                                    "errorCode" => 'OTHER_ERROR',
                                    "statusCode" => 500
                                ]
                            ], 500);
                        } 
                    }
                } else {
                    echo "<script>console.log('Account : login : error:  Token.destroyAll : failed to delete.')</script>";
                    return response()->json([ 
                        "error" => [
                            "statusCode" => "500",
                            "errorCode" => "OTHER_ERROR", 
                            "message" => "Couldn\'t create new tokens : failed to delete. Please contact us if this persists" 
                        ]
                    ], 500);
                }
                //TODO where?
                return response()->json([ 
                    "result" => [
                        "status" => "ok", 
                        "token" => $newToken, 
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
            die($exception);
            return response()->json([ 
                "token" => [
                    "status" => "error", 
                    "message" => "email not found" 
                ]
            ], 400);
        }
    }

    private function sendTokenToGateway($sha256Token) {
        $ApiKeyGroup = 'UrbanCrawlUserCollectionTest';
        try {
            $client = \Akamai\Open\EdgeGrid\Client::createFromEdgeRcFile('papi', '../config/.edgerc');
            $response = $client->get('/apikey-manager-api/v1/collections');
            $responseCode = $response->getStatusCode();
            $collections = json_decode($response->getBody()->getContents(), true);
            echo "<script>console.log('sendTokenToGateway: Listing all collections...')</script>\n\n";
            echo "<script>console.log('sendTokenToGateway: Status: ," . $responseCode . "')</script>\n\n";
            echo "<script>console.log('sendTokenToGateway: Data: ," . json_encode($collections)  . "')</script>\n\n";
            if($responseCode != 200) {
                echo "<script>console.log('" . $responseCode . ", Unable to retrieve key collections')</script>\n\n";
                //TODO statusmessate
                throw new Exception('Unable to retrieve key collections');
            }
            if(count($collections) > 0){
                foreach($collections as $collection) {
                    if($collection['name'] == $ApiKeyGroup) {
                        echo "<script>console.log('sendTokenToGateway: Collection present, going to send token')</script>\n\n";
                        if( !$this->sendToken($collection['id'], $sha256Token) ) {
                            throw new Exception('Unable to create key');
                        }
                        return true;
                    }
                }
            }
            echo "<script>console.log('sendTokenToGateway: Collection not present, going to create a collection')</script>\n\n";
            if( !$this->createNewCollectionAndSendToken($sha256Token) ) {
                throw new Exception('Unable to create key');
            }
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function sendToken($collectionId, $sha256Token) {
        try {
            $client = \Akamai\Open\EdgeGrid\Client::createFromEdgeRcFile('papi', '../config/.edgerc');
            $response = $client->request('POST', 
                '/apikey-manager-api/v1/keys', 
                [
                    "headers" => ['Content-Type' => 'application/json'],
                    "body" => json_encode([
                        "collectionId" => $collectionId,
                        "mode" => 'CREATE_ONE',
                        "tags" => ['single', 'new'],
                        "value" => $sha256Token,
                        "label" => 'Access Token',
                        "description" => 'Access Token for Urban Crawl User'
                    ])
                ]
            );
            if($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $token = $response->getBody()->getContents();
                echo "<script>console.log('sendToken: Data " . $token . "')</script>\n\n";
                echo "<script>console.log('sendToken Status: " . $response->getStatusCode() . "')</script>\n\n";
                return true;
            }
            return false;
        } catch(\Exception $exception) {
            return false;
        }
    }

    private function createNewCollectionAndSendToken($sha256Token) {
        try {
            $client = \Akamai\Open\EdgeGrid\Client::createFromEdgeRcFile('papi', '../config/.edgerc');
            $response = $client->post( 
                '/apikey-manager-api/v1/collections', 
                [
                    "headers" => ['Content-Type' => 'application/json'],
                    "body" => json_encode([
                        "name" => 'UrbanCrawlUserCollectionTest',
                        "contractId" => 'C-1FRYVV3',
                        "groupId" => 95357,
                        "description" => 'Collection for UrbanCrawl Users',
                        "quota" => [
                            "enabled" => true,
                            "value" => 100,
                            "interval" => 'HOUR_1',
                            "headers" => [
                                "denyLimitHeaderShown" => true,
                                "denyRemainingHeaderShown" => true,
                                "denyNextHeaderShown" => true,
                                "allowLimitHeaderShown" => true,
                                "allowRemainingHeaderShown" => true,
                                "allowResetHeaderShown" => true
                            ],
                        ]
                    ])
            ]
            );
            $collection = json_decode($response->getBody()->getContents(), true);
            echo "<script>console.log('createNewCollectionAndSendToken: Listing all collections...')</script>\n\n";
            echo "<script>console.log('createNewCollectionAndSendToken: Data " . json_encode($collection) . "')</script>\n\n";
            echo "<script>console.log('createNewCollectionAndSendToken: Response status: " . $response->getStatusCode() . "')</script>\n\n";
            
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                if (!$this->sendToken($collection['id'], $sha256Token)) {
                    throw new Exception('Unable to create key');
                }
            } else {
                throw new Exceptigon('Unable to create collection');
            }
        } catch(\Exception $exception) {
            return false;
        }
        return true;
    }
    
    

    public function getProfile(Request $request, $apiVersion)
    {
        if($apiVersion != "v2"){
            echo "<script>console.log('Account : getProfile : error: invalid API version')</script>";
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
                    //TODO -error msg
                    echo "<script>console.log('Account : returnUserProfile : error: Account.find: , err')</script>";
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
            echo "<script>console.log('Account : getProfile : error: Auth Required, sentToken was : , " . $request->header('authorization') ."')</script>";
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
            echo "<script>console.log('Account : updateProfile : error: invalid API version')</script>";
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
                        if(isset($request->email)) { 
                            $valuesToUpdate["email"] = $request->email; 
                        }
                        if(isset($request->full_name)) { 
                            $valuesToUpdate["full_name"] = $request->full_name ; 
                        }
                        if(isset($request->password)) { 
                            $valuesToUpdate["password"] = app('hash')->make( $request->password, ['rounds' => $this->saltRounds] ); 
                        }
                        $account = Account::where('userid', $verifiedToken->value('userid'))->firstOrFail();
                        $account->update( $valuesToUpdate );
                        return response()->json([
                            "result" => [
                                "status" => "ok",
                                "updated_profile" => $valuesToUpdate
                            ]
                        ], 200);
                    } catch (\Exception $exception){
                        echo "<script>console.log('Account : saveProfileUpdates : error: Account.update: , err')</script>";
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
            echo "<script>console.log('Account : updateProfile : error: Auth Required, sentToken was : , " . $request->header('authorization') ."')</script>";
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