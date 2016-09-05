<?php

use Illuminate\Http\Request;

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
Route::group(['middleware' => ['api']], function () {
    Route::get('/file', 'FileController@index');
    Route::get('/file/download/{name}', 'FileController@download');
    Route::post('/file/upload', 'FileController@upload');
    Route::delete('/file/delete/{name}', 'FileController@delete');
    Route::get('/file', 'FileController@index');

});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

