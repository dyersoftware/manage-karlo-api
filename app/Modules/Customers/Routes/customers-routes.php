<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */




$routes->group('api/customers', ['filter' => 'jwt'], function ($routes) {

    $routes->get('/', '\App\Modules\Customers\Controllers\CustomersController::index');
    $routes->get('(:num)', '\App\Modules\Customers\Controllers\CustomersController::show/$1');
    $routes->post('/', '\App\Modules\Customers\Controllers\CustomersController::create');
    $routes->put('(:num)', '\App\Modules\Customers\Controllers\CustomersController::update/$1');
    $routes->delete('(:num)', '\App\Modules\Customers\Controllers\CustomersController::delete/$1');
    $routes->post('assign', '\App\Modules\Customers\Controllers\CustomersController::assignCustomer');
    $routes->post('unassign', '\App\Modules\Customers\Controllers\CustomersController::unassignCustomer');
    $routes->get('assigned-customers', '\App\Modules\Customers\Controllers\CustomersController::assignedCustomers');
});
