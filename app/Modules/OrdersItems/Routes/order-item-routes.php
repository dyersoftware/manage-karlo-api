<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('api/order-items', ['filter' => 'jwt'], function ($routes) {

    // ✅ CRUD
    $routes->get('/', '\App\Modules\OrdersItems\Controllers\OrderItemController::index');

    $routes->get('(:num)', '\App\Modules\OrdersItems\Controllers\OrderItemController::show/$1');

    $routes->post('/', '\App\Modules\OrdersItems\Controllers\OrderItemController::create');

    $routes->put('(:num)', '\App\Modules\OrdersItems\Controllers\OrderItemController::update/$1');

    $routes->delete('(:num)', '\App\Modules\OrdersItems\Controllers\OrderItemController::delete/$1');

    // ✅ Update Status
    $routes->patch('(:num)/status', '\App\Modules\OrdersItems\Controllers\OrderItemController::updateStatus/$1');
});
