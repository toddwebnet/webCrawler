<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return \App\Services\Responses\ApiResponse::successResponse('pong');
});

Route::post('token/get', 'TokenController@get');
Route::get('token/get', 'TokenController@get');
Route::middleware(\App\Http\Middleware\ApiAuthMiddleware::class)->group(function () {
    Route::get('html/pop', 'HtmlController@pop');
    Route::get('html/mark-processed/{id}', 'HtmlController@markProcessed');

});

