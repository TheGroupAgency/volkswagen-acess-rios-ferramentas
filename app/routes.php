<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array('as' => 'home'), function () {
	return null;
});

Route::post('/sendOil', array('as' => 'oil.sendOil', 'uses' => 'OilController@sendOil'));

Route::post('/crypt', array('as' => 'crypt.index', 'uses' => 'CryptController@index'));

Route::group(array('before' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin'), function()
{
	Route::get('login', array('as' => 'admin.login', 'uses' => 'AuthController@login'));
	Route::post('login', array('as' => 'admin.postLogin', 'uses' => 'AuthController@postLogin'));
	Route::get('logout', array('as' => 'admin.logout', 'uses' => 'AuthController@logout'));
	
	// Economy
	Route::get('/', array('as' => 'admin.home', 'uses' => 'HomeController@index'));
	Route::get('economy', array('as' => 'admin.economy', 'uses' => 'EconomyController@index'));
	Route::post('economy', array('as' => 'admin.economy.upload', 'uses' => 'EconomyController@upload'));

	// Tires
	Route::get('tire', array('as' => 'admin.tire', 'uses' => 'TireController@index'));
	Route::post('tire', array('as' => 'admin.tire.upload', 'uses' => 'TireController@upload'));

	// Oil
	Route::get('oil', array('as' => 'admin.oil', 'uses' => 'OilController@index'));
	Route::post('oil', array('as' => 'admin.oil.upload', 'uses' => 'OilController@upload'));

	Route::post('api/sheet/rollback', array('as' => 'admin.api.sheet.rollback', 'uses' => 'SheetController@rollback'));
	Route::get('api/sheet/{type}', array('as' => 'admin.api.sheet.paginate', 'uses' => 'SheetController@paginate'));

});

Route::group(array('prefix' => 'economy'), function () {
	Route::get('/', array('as' => 'economy.index', 'uses' => 'EconomyController@index'));
});

Route::group(array('prefix' => 'tire'), function () {
	Route::get('/', array('as' => 'tire.index', 'uses' => 'TireController@index'));

	Route::post('api/getVehicleTireSize', array('as' => 'tire.api.getVehicleTireSize', 'uses' => 'TireController@getVehicleTireSize'));
	Route::post('api/getVehicleTire', array('as' => 'tire.api.getVehicleTire', 'uses' => 'TireController@getVehicleTire'));
});

Route::group(array('prefix' => 'oil'), function () {
	Route::get('/', array('as' => 'oil.index', 'uses' => 'OilController@index'));
	
	Route::post('api/getOil', array('as' => 'oil.api.getOil', 'uses' => 'OilController@getOil'));


});
