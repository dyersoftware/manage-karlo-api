<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('test', 'Api\TestController::index');

$routes->group('api', function ($routes) {
    $routes->post('register', 'Api\AuthController::register');
    $routes->post('login', 'Api\AuthController::login');
    $routes->post('refresh', 'Api\AuthController::refresh');
    $routes->post('logout', 'Api\AuthController::logout');

    $routes->group('', ['filter' => 'jwt'], function ($routes) {

        // Customers CRUD
        $routes->get('customers', 'Api\CustomerController::index');
        $routes->get('customers/(:num)', 'Api\CustomerController::show/$1');
        $routes->post('customers', 'Api\CustomerController::create'); // ✅ IMPORTANT
        $routes->put('customers/(:num)', 'Api\CustomerController::update/$1');
        $routes->delete('customers/(:num)', 'Api\CustomerController::delete/$1');
8   });
});
