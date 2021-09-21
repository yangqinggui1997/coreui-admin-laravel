<?php

namespace App\Http\Middleware;

use App\Services\FirebaseServices;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if($request->ajax() || $request->wantsJson())
        {
            $token = $request->bearerToken();
            if(!$token)
                return response()->json(["errorMessage" => "Token not provided.", "errorCode" => "token-not-provide"], 401);
            else
            {
                $checkToken = FirebaseServices::verifiedToken($token);
                if($checkToken->status)
                {
                    $request->auth = (object)$checkToken->user;
                    return $next($request);
                }
                return response()->json(["errorMessage" => "Token is expired.", "errorCode" => "token-expired"], 401);
            }
        }
        else
        {
            $token = $request->bearerToken() ? $request->bearerToken() : Session::get('firebaseIdToken');
            if(!$token)
                return redirect()->route('signinPage')->withErrors('Token not provided.');
            $checkToken = FirebaseServices::verifiedToken($token);
            if($checkToken->status)
            {
                $request->auth = (object)$checkToken->user;
                return $next($request);
            }
            return redirect()->route('signinPage')->withErrors($checkToken->errorMessage);
        }
    }
}
