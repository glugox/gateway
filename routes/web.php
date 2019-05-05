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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {

    /** Authorisation */
    $router->post('login', [ 'uses' => 'AuthController@login']);

    $router->group(['middleware' => ['auth:api']], function () use ($router) {

            /**
             * Before getting into services, let the Gateway handle its own duties,
             * like settings.
             */
            $router->get('settings', 'SettingsController@index');

            /**
             * All request methods go to ForwardController@forward. The controller htan
             * knows what to do based on that and url path varisbles.
             */
            foreach (['get', 'delete', 'head', 'patch', 'post', 'put'] as $method) {
                $router->$method('{service}[/{resource}[/{action}]]', 'ForwardController@forward');
            }

    });
    
});
