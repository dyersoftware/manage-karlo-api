<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('test', 'Api\TestController::index');

require APPPATH . 'Modules/Auth/Routes/auth.php';
require APPPATH . 'Modules/Customers/Routes/customers-routes.php';
