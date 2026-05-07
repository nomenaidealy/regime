<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */

$routes->get('/',             'Home::index');
$routes->get('login',         'UserController::loginPage');
$routes->post('login',        'UserController::login');
$routes->get('inscription',   'UserController::inscription');
$routes->post('inscription',  'UserController::saveUser');
$routes->get('logout',        'UserController::logout');