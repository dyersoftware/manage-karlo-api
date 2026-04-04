<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('api/auth', function ($routes) {
    $routes->post('register', '\App\Modules\Auth\Controllers\AuthController::register');
    $routes->post('login', '\App\Modules\Auth\Controllers\AuthController::login');
    $routes->post('refresh', '\App\Modules\Auth\Controllers\AuthController::refresh');
    $routes->post('logout', '\App\Modules\Auth\Controllers\AuthController::logout');
});
