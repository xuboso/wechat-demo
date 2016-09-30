<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/wechat', 'WechatController@checkValidate')->name('wechatValidate');
Route::post('/wechat', 'WechatController@process');

Route::group(['namespace' => 'Wechat'], function() {
    Route::resource('/wechat/menus', 'MenuController');
    Route::get('wechat/generate', 'QrcodeController@generate');
});