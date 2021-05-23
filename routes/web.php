<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('register', 'AuthController@register');
    $router->get('current', 'AuthController@current');
});

$router->group(['prefix' => 'check'], function () use ($router) {
    $router->get(
        '/is-admin-retail',
        [
            'middleware' => ['auth', 'admin_retail'],
            'uses' => 'UserCheckController@isAdminRetail'
        ]
    );
    $router->get(
        '/is-admin-store',
        [
            'middleware' => ['auth', 'admin_store'],
            'uses' => 'UserCheckController@isAdminStore'
        ]
    );
    $router->get(
        '/is-customer',
        [
            'middleware' => ['auth', 'customer'],
            'uses' => 'UserCheckController@isCustomer'
        ]
    );
});
