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

Route::post('user/register','UsersController@register');
Route::post('auth/login','UsersController@login');
//Route::get('open', 'DataController@open');

    Route::post('user','UsersController@index');
Route::group(['middleware' => ['jwt.verify']], function() {

});

