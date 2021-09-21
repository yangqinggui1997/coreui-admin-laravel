<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::prefix('user')->group(function(){

    Route::get('reset_password', 'admin\\UserController@resetPassword')->name('resetPassword');

    Route::get('refreshToken', 'admin\\UserController@refreshToken')->name('refreshToken');

    Route::get('signup', 'admin\\UserController@signup')->name('signup');

    Route::post('signin', 'admin\\UserController@signin')->name('signin');
});