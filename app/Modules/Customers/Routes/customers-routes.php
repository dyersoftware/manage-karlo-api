<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */




$routes->group('api/customers', ['filter' => 'jwt'], function ($routes) {

    $routes->get('/', '\App\Modules\Customers\Controllers\CustomerController::index');
    $routes->get('(:num)', '\App\Modules\Customers\Controllers\CustomerController::show/$1');
    $routes->post('/', '\App\Modules\Customers\Controllers\CustomerController::create');
    $routes->put('(:num)', '\App\Modules\Customers\Controllers\CustomerController::update/$1');
    $routes->delete('(:num)', '\App\Modules\Customers\Controllers\CustomerController::delete/$1');
});
