<?php
use Illuminate\Http\Request;

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

$router->post('login/','UsersController@auth');
$router->post('createuser/','UsersController@create');

$router->post('accesstoken/','UsersController@accesstoken');
$router->post('getnearestplaza/','UsersController@getnearestplaza');
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('logout/','UsersController@logout');
    $router->get('mydetail/','UsersController@mydetail');
    // $app->get('user/profile', function () {
    //     // Uses Auth Middleware
    // });
});


// $router->get('/test_endpoint', function (Request $request) {
    
// })->middleware('client');