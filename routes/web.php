<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->group(['prefix' => 'api/'], function ($router) {
	$router->post('login/','UserController@authenticate');
	$router->post('register/','UserController@register');

	$router->group(['middleware' => 'auth'], function ($router) {
		// User
		$router->get('user','UserController@currentUser');

		// Customer
		$router->get('customers','CustomerController@index');
		$router->post('customers','CustomerController@store');
		$router->get('customers/{id}','CustomerController@show');
		$router->put('customers/{id}','CustomerController@update');
		$router->delete('customers/{id}','CustomerController@destroy');

		// Booking
		$router->get('bookings','BookingController@index');
		$router->post('bookings','BookingController@store');
		$router->get('bookings/{id}','BookingController@show');
		$router->put('bookings/{id}','BookingController@update');
		$router->delete('bookings/{id}','BookingController@destroy');

		// Room
		$router->get('rooms','RoomController@index');
		$router->post('rooms','RoomController@store');
		$router->get('rooms/{id}','RoomController@show');
		$router->put('rooms/{id}','RoomController@update');
		$router->delete('rooms/{id}','RoomController@destroy');
	});
});
