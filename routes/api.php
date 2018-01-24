<?php

use Illuminate\Http\Request;

use App\Message;


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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([
        'middleware' => 'auth:api',
        'prefix' => 'messages'
    ], function() {
        Route::post('', 'MessageController@message');
        Route::post('{message}/answer', 'MessageController@answer');
    }
);

Route::group(['prefix' => 'messages'], function() {
    Route::get('', 'MessageController@index');
    Route::get('count', 'MessageController@count');
    Route::get('paged/{page}/{per_page}', 'MessageController@index')
        ->where('page', '[0-9]+')
        ->where('per_page', '[0-9]+')
    ;
});

Route::post('register', 'Auth\RegisterController@register');

Route::post('login', 'Auth\LoginController@login');

Route::get('logout', 'Auth\LoginController@logout');

Route::get('test/event', function() {
    event(new \App\Events\NewMessage('Test User', "Test Message"));
    return response()->json('OK', 200);
});
