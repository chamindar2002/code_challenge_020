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

$router->group(['middleware' => ['auth:api'],'prefix'=>'api/v1'], function () use ($router){   
    $router->post('post', 'PostController@store');
    $router->post('post/{uuid}/submit-to-medium', 'PostController@submitToMedium');
    $router->post('post/{uuid}/upload-image', 'PostController@uploadImage');

    $router->post('user', 'UserController@store');
});