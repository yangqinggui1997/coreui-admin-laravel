<?php

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
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
Route::get('test', function(){
    echo "hi there!";
});

Route::post('readTextImage', function(Request $request){
    $imageAnnotatorClient = new ImageAnnotatorClient();
    try
    {
        // return response()->json(["data" => $request->hasFile('image')]);
        $imageContent = file_get_contents($request->file('image'));
        $response = $imageAnnotatorClient->textDetection($imageContent);
        if($response->hasError())
            throw new Error($response->getError());
        
    }
    catch(\Throwable $err)
    {
        $imageAnnotatorClient->close();
        return response()->json(["error" => $err->getMessage()], 422);
    }

    $imageAnnotatorClient->close();

    return response()->json(["data" => $response->getTextAnnotations()]);
})->name("readTextImage");

Route::post('lineBotCallback', 'LineBotController@messages')->name("line.message");

Route::prefix('user')->group(function(){

    Route::get('reset_password', 'admin\\UserController@resetPassword')->name('resetPassword');

    Route::get('refreshToken', 'admin\\UserController@refreshToken')->name('refreshToken');

    Route::get('signup', 'admin\\UserController@signup')->name('signup');

    Route::post('signin', 'admin\\UserController@signin')->name('signin');
});