<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->options('(:any)', function () {
    return response()->setStatusCode(200);
});

$routes->get('/', 'Home::index');
$routes->get('test', 'Api\TestController::index');

require APPPATH . 'Modules/Auth/Routes/auth.php';
require APPPATH . 'Modules/Customers/Routes/customers-routes.php';
require APPPATH . 'Modules/Orders/Routes/orders-routes.php';
require APPPATH . 'Modules/Payments/Routes/payment-routes.php';
require APPPATH . 'Modules/OrdersItems/Routes/order-item-routes.php';
