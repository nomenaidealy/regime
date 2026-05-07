<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */

$routes->get('/',             'Home::index');
$routes->get('login',         'User::loginPage');
$routes->post('login',        'User::login');
$routes->get('inscription',   'User::inscription');
$routes->post('inscription',  'User::saveUser');
$routes->get('logout',        'User::logout');