<?php

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

use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->post('user/register', 'AuthController@register');
$router->post('user/login', 'AuthController@login');

$router->group(['middleware' => 'auth'], function () use ($router) {
    //User
    $router->post('user/update/{id}', 'UserController@userUpdate');
    $router->get('user/{id}', 'UserController@getUserById');
    $router->get('user/data/all', 'UserController@getAllUser');
    $router->get('user/ustad/all', 'UserController@getUstad');

    //Location
    $router->post('location/add', 'LocationController@addLocation');
    $router->post('location/update/{id}', 'LocationController@updateLocation');
    $router->get('location/all', 'LocationController@getAllLocationData');

    //Event
    $router->post('event/jenis/add', 'EventController@addJenisEvent');
   
    $router->get('event/show/{id}', 'EventController@event');
    $router->get('event/all', 'EventController@allEvent');

    //postingan
   
   
    $router->get('postingan/data/{id}', 'PostController@getPostinganById');

  

    //PostinganUser
    $router->get('user/postingan/{id}', 'UserController@postingan');
    
});
$router->post('event/add', 'EventController@addEvent');
$router->get('user/postingan/{id}', 'UserController@postingan');
$router->post('login', 'AuthController@login_post');

$router->post('cari', 'UserController@searchUser');
$router->get('jenisevent', 'EventController@getAllJenisEvent');
 $router->post('postingan/add', 'PostController@addPostingan');
 $router->get('postingan/all', 'PostController@getAllPostingan');
 $router->get('posl', 'EventController@addMesjid');

 $router->post('search/mesjid', 'EventController@searchmesjid');
 $router->post('ustadd', 'UserController@addUstad');
 $router->get('getPost', 'PostController@getPost');
 $router->get('gettt', 'EventController@getEventHome');


 $router->get('ram', 'EventController@getRam');

 $router->post('getUserData', 'UserController@getUserData');


 $router->get('g', 'AuthController@ll');




 