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
    return view('welcome');
});

Route::get('/process', 'MobicredController@process');
Route::get('/auth', 'MobicredController@auth');
Route::get('/result', 'MobicredController@result');
Route::get('/resend', 'MobicredController@resendOTP');

Route::get('/checkstatus', function () {
    return view('status');
});
Route::get('/status', 'MobicredController@status');

Route::get('/getrefund', function () {
    return view('refund');
});
Route::get('/refund', 'MobicredController@refund');
