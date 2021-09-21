<?php

namespace App\Http\Middleware;

use App\Services\FirebaseServices;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    
    protected $except = [
        // 'user/login',
        'user/create'
    ];

    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->bearerToken() ? $request->bearerToken() : Session::get('firebaseIdToken');
        if(!$token)
            return $next($request);
        $checkToken = FirebaseServices::verifiedToken($token);
        if($checkToken->status)
            return redirect()->route('home');
        return $next($request);
    }
}
