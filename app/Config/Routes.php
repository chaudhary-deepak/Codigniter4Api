<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group("api", function ($routes) {
    $routes->post("signup", "Api\AuthController::signup");
    $routes->post("login", "Api\AuthController::login");
    $routes->get("users", "Api\ApiController::users", ['filter' => 'authFilter']);
});
