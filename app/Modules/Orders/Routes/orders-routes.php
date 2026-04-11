<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */




$routes->group('api/orders', ['filter' => 'jwt'], function ($routes) {

    $routes->get('/', '\App\Modules\Orders\Controllers\OrderController::index');
    $routes->get('(:num)', '\App\Modules\Orders\Controllers\OrderController::show/$1');
    $routes->post('/', '\App\Modules\Orders\Controllers\OrderController::create');
    $routes->put('(:num)', '\App\Modules\Orders\Controllers\OrderController::update/$1');
    $routes->delete('(:num)', '\App\Modules\Orders\Controllers\OrderController::delete/$1');
});
