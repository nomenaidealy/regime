<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */

$routes->get('/',              'Home::index');
$routes->get('login',           'UserController::loginPage');
$routes->post('login',          'UserController::login');
$routes->get('inscription',     'UserController::inscription');
$routes->post('inscription',    'UserController::saveUser');
$routes->post('api/validate-step1', 'UserController::validateStep1');
$routes->post('api/validate-step2', 'UserController::validateStep2');
$routes->post('api/validate-step3', 'UserController::validateStep3');
$routes->post('api/complete-inscription', 'UserController::completeInscription');
$routes->get('logout',          'UserController::logout');

$routes->get('admin/regimes/create',    'RegimeController::form');
// Edit regime
$routes->get('admin/regimes/edit/(:num)',    'RegimeController::edit/$1');

$routes->get('admin/dashboard', 'AdminController::dashboard');

$routes->get('admin/regimes',  'RegimeController::list');

$routes->post('admin/regimes/save',  'RegimeController::save');

$routes->post('admin/regimes/update/(:num)',  'RegimeController::update/$1');
$routes->get('admin/regimes/delete/(:num)',  'RegimeController::delete/$1');
// Sports CRUD
$routes->get('admin/sports',          'SportController::list');
$routes->get('admin/sports/create',   'SportController::form');
$routes->post('admin/sports/save',    'SportController::save');
$routes->get('admin/sports/edit/(:num)',   'SportController::form/$1');
$routes->get('admin/sports/delete/(:num)', 'SportController::delete/$1');

