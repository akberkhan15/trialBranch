<?php

use Illuminate\Http\Request;

header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

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

Route::post('getnearestplaza','Api\v1\GeoSearchController@getnearestplaza');
Route::post('addplaza','Api\v1\GeoSearchController@addplaza');
Route::post('editplaza','Api\v1\GeoSearchController@editplaza');
Route::post('deleteplaza','Api\v1\GeoSearchController@deleteplaza');

Route::post('login','Api\v1\UserController@login');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::group(['middleware' => 'auth.jwt'], function () {
// 	//Route::get('afterlogin','TestController@afterlogin');
// 	Route::get('test','Api\v1\UserController@test');
// });


Route::group(['middleware' => ['auth.jwt','profile-status']], function () {

	/* User Start */
	Route::post('changepassword','Api\v1\UserController@changepassword');
	Route::post('getusers','Api\v1\UserController@getusers');
	Route::post('changeprofileimage','Api\v1\UserController@changeprofileimage');
	Route::post('unblockuser','Api\v1\UserController@unblockuser');
	Route::get('beforeadduser','Api\v1\UserController@beforeadduser');
	/* User End */

	/* Ip Pool Start */
	Route::post('addippool','Api\v1\IpPoolController@addippool');
	Route::post('getippool','Api\v1\IpPoolController@getippool');
	Route::post('changestatusippool','Api\v1\IpPoolController@changestatusippool');
	Route::post('deleteippool','Api\v1\IpPoolController@deleteippool');
	/* Ip Pool End */

	/* AccountType Start */
	Route::get('getaccounttype','Api\v1\AccountTypeController@getaccounttype');
	/* AccountType End */

	/* Permission Start */
	Route::get('getpermissions','Api\v1\PermissionsController@getpermissions');
	/* Permission End */
	Route::get('test','Api\v1\UserController@test');
});

Route::group(['middleware' => ['auth.jwt']], function () {
	Route::get('logout','Api\v1\UserController@logout');
	Route::post('beforeresetpassword','Api\v1\UserController@beforeresetpassword');
	Route::post('resetpassword','Api\v1\UserController@resetpassword');
	Route::post('beforecompleteprofile','Api\v1\UserController@beforecompleteprofile');
	Route::post('completeprofile','Api\v1\UserController@completeprofile');

});
