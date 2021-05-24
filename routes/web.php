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

$router->group(
    ['prefix' => 'store'],
    function () use ($router) {
        $router->get('', 'StoreController@index');
        $router->get('{id}', 'StoreController@get');
        $router->group(['middleware' => ['auth', 'user_type:admin_retail']], function () use ($router) {
            $router->post('', 'StoreController@store');
            $router->put('{id}', 'StoreController@update');
            $router->delete('{id}', 'StoreController@destroy');
        });
    }
);

$router->group(
    ['prefix' => 'brand'],
    function () use ($router) {
        $router->get('', 'BrandController@index');
        $router->get('{id}', 'BrandController@get');
        $router->group(['middleware' => ['auth', 'user_type:admin_retail']], function () use ($router) {
            $router->post('', 'BrandController@store');
            $router->put('{id}', 'BrandController@update');
            $router->delete('{id}', 'BrandController@destroy');
        });
    }
);

$router->group(
    ['prefix' => 'vendor'],
    function () use ($router) {
        $router->get('', 'VendorController@index');
        $router->get('{id}', 'VendorController@get');
        $router->group(['middleware' => ['auth', 'user_type:admin_retail']], function () use ($router) {
            $router->post('', 'VendorController@store');
            $router->put('{id}', 'VendorController@update');
            $router->delete('{id}', 'VendorController@destroy');
        });
    }
);

$router->group(
    ['prefix' => 'product-type'],
    function () use ($router) {
        $router->get('', 'ProductTypeController@index');
        $router->get('{id}', 'ProductTypeController@get');
        $router->group(['middleware' => ['auth', 'user_type:admin_retail']], function () use ($router) {
            $router->post('', 'ProductTypeController@store');
            $router->put('{id}', 'ProductTypeController@update');
            $router->delete('{id}', 'ProductTypeController@destroy');
        });
    }
);

$router->group(
    ['prefix' => 'product'],
    function () use ($router) {
        $router->get('', 'ProductController@index');
        $router->get('{id}', 'ProductController@get');
        $router->group(['middleware' => ['auth', 'user_type:admin_retail']], function () use ($router) {
            $router->post('', 'ProductController@store');
            $router->post('{id}', 'ProductController@update');
            $router->delete('{id}', 'ProductController@destroy');
        });
    }
);
