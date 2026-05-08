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

// Admin login
$routes->get('admin',           'AdminController::index');
$routes->post('admin/tolog',    'AdminController::tolog');

// Gold option
$routes->get('gold/activate',   'GoldController::activate');

// Code Promo
$routes->get('codepromo/form',          'CodePromoController::form');
$routes->post('codepromo/redeem',       'CodePromoController::redeem');

// User dashboard
$routes->get('dashboard', 'UserController::dashboard');
$routes->get('profil',    'UserController::profil');

// Admin area protected by filter
$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
	$routes->get('dashboard', 'AdminController::dashboard');
	$routes->get('logout', 'AdminController::logout');
	$routes->get('codepromo/create',  'CodePromoController::adminForm');
	$routes->post('codepromo/create', 'CodePromoController::adminCreate');
	$routes->get('codepromo/list',    'CodePromoController::adminList');

	// Edit regime
	$routes->get('regimes/create',        'RegimeController::form');
	$routes->get('regimes/edit/(:num)',   'RegimeController::edit/$1');
	$routes->get('regimes',               'RegimeController::list');
	$routes->post('regimes/save',         'RegimeController::save');
	$routes->post('regimes/update/(:num)','RegimeController::update/$1');
	$routes->get('regimes/delete/(:num)', 'RegimeController::delete/$1');

	// Sports CRUD
	$routes->get('sports',                    'SportController::list');
	$routes->get('sports/create',             'SportController::form');
	$routes->post('sports/save',              'SportController::save');
	$routes->post('sports/update/(:num)',     'SportController::update/$1');
	$routes->get('sports/edit/(:num)',        'SportController::edit/$1');
	$routes->get('sports/delete/(:num)',      'SportController::confirmDelete/$1');
	$routes->post('sports/force-delete/(:num)','SportController::forceDelete/$1');
	$routes->post('sports/remove-keep-diets/(:num)', 'SportController::removeKeepDiets/$1');
});

