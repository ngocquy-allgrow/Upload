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

Route::namespace('Api')->group(function () {
    Route::post('auth/social', 'SocialAuthController@authSocial');

    Route::group(['middleware' => 'ApiMicroService', 'prefix' => 'user'], function () {
        Route::post('create-customer', 'CustomerController@createCustomer');
        Route::post('login', 'CustomerController@loginCustomer');
        Route::post('fogot-password', 'CustomerController@fogotPassword');

        Route::group(['middleware' => 'jwt.auth'], function () {
            Route::post('detail', 'CustomerController@detailCustomer');
            Route::post('list', 'CustomerController@getAllCustomer');
            Route::post('logout', 'CustomerController@logoutCustomer');
            Route::post('delete', 'CustomerController@deleteCustomer');
            Route::post('update', 'CustomerController@updateCustomer');
            Route::post('refresh', 'CustomerController@refreshCustomer');
            Route::post('change-pass', 'CustomerController@changePassword');
        });
    });
});