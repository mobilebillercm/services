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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('services', 'ApiController@createService')->middleware('token.verification');
Route::get('services', 'ApiController@getAllService')->middleware('token.verification');
Route::get('services/{id}', 'ApiController@getServiceByBidOrByName')->middleware('token.verification');
Route::put('services/{id}', 'ApiController@updateService')->middleware('token.verification');
Route::delete('services/{id}', 'ApiController@deleteService')->middleware('token.verification');
Route::get('services/{id}/icon', 'ApiController@getIcon')->middleware('token.verification');
Route::put('services/{id}/active', 'ApiController@activateOrDeactivateService')->middleware('token.verification');

