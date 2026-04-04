<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('test', 'Api\TestController::index');

$routes->group('api', function ($routes) {


    $routes->group('', ['filter' => 'jwt'], function ($routes) {

        // Customers CRUD
        $routes->get('customers', 'Api\CustomerController::index');
        $routes->get('customers/(:num)', 'Api\CustomerController::show/$1');
        $routes->post('customers', 'Api\CustomerController::create'); // ✅ IMPORTANT
        $routes->put('customers/(:num)', 'Api\CustomerController::update/$1');
        $routes->delete('customers/(:num)', 'Api\CustomerController::delete/$1');
    });
});

require APPPATH . 'Modules/Auth/Routes/auth.php';
