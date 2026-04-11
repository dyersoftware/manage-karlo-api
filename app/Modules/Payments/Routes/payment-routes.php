<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('api/payments', ['filter' => 'jwt'], function ($routes) {

    // ✅ Create Payment
    $routes->post('/', '\App\Modules\Payments\Controllers\PaymentController::create');

    // ✅ Get payments by order
    $routes->get('order/(:num)', '\App\Modules\Payments\Controllers\PaymentController::byOrder/$1');

    // ✅ Delete payment
    $routes->delete('(:num)', '\App\Modules\Payments\Controllers\PaymentController::delete/$1');
    //get by user with optional customer_id filter
    $routes->get('/', '\App\Modules\Payments\Controllers\PaymentController::index');
});
