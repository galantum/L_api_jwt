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

/*Route::group(['middleware' => ['web']], function () {

    Route::get('/', function () {
        return view('welcome');
    });

});*/

#create route for registration
Route::post('/register', 'UserController@register');

#Create nested group route
Route::group(['middleware' => ['web']], function () {
	#Create route for Login
	Route::post('/login', 'UserController@login');

	#create route group for user access his data
	Route::group(array('prefix' => 'api', 'middleware' => 'jwt'), function(){
		#Create route for get data profile User
		Route::get('/user', 'UserController@show');
		#Create Route for edit data profile User
		Route::put('/user/edit', 'UserController@edit');
		#Create Route for resetToken
		Route::get('/user/resetToken', 'UserController@resetToken');
	});
});