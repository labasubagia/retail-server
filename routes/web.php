<?php

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
            'middleware' => ['auth', 'user_type:admin_retail'],
            'uses' => 'UserCheckController@isAdminRetail'
        ]
    );
    $router->get(
        '/is-admin-store',
        [
            'middleware' => ['auth', 'user_type:admin_store'],
            'uses' => 'UserCheckController@isAdminStore'
        ]
    );
    $router->get(
        '/is-customer',
        [
            'middleware' => ['auth', 'user_type:customer'],
            'uses' => 'UserCheckController@isCustomer'
        ]
    );
});
