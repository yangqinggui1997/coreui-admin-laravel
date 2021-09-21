<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function refreshToken()
    {
        $validator = Validator::make($this->request->all(), [
            'refreshToken' => 'required|string'
        ]);

        if($validator->fails())
            return response()->json(["errorMessage" => $validator->getMessageBag(), "errorCode" => "validate-fail"], 422);
        $user = UserService::signInWithRefreshToken($this->request->input('refreshToken'));
        if($user->status)
        {
            return response()->json(["tokens" => $user->tokens]);
        }
        else return response()->json(["errorMessage" => $user->errorMessage, "errorCode" => $user->errorCode], 422);
    }
}
