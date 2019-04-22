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

Route::get('/pay/{payment_type}/{username}/{amount}', 'PayController@index')
	->where([
		'payment_type' => '[a-zA-Z0-9_-]+',
		'username' => '[a-zA-Z0-9_-]+',
		'amount' => '[0-9\.]+',
	]);
Route::get('/success/{vendor?}', 'SuccessController@index')
	->where([
		'vendor' => '[a-zA-Z0-9_-]+',
	]);
Route::match(['get', 'post'], '/callback/{vendor}/{payment_type}/{username}/{datetime}', 'CallbackController@index')
	->where([
		'vendor' => '[a-zA-Z0-9_-]+',
		'payment_type' => '[a-zA-Z0-9_-]+',
		'username' => '[a-zA-Z0-9_-]+',
		'datetime' => '[0-9]+',
	]);



/*
Route::get('/kyo/test', 'KyoController@test');
Route::post('/kyo/test', 'KyoController@test');
*/
