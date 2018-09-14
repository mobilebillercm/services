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

Route::post('services', 'ApiController@createService');
Route::get('services', 'ApiController@getAllService');
Route::get('services/{id}', 'ApiController@getServiceByBidOrByName');
Route::put('services/{id}', 'ApiController@updateService');
Route::delete('services/{id}', 'ApiController@deleteService');
Route::get('services/{id}/icon', 'ApiController@getIcon');
Route::put('services/{id}/active', 'ApiController@activateOrDeactivateService');

