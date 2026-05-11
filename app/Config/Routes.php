
<?php
use CodeIgniter\Router\RouteCollection;


/** @var RouteCollection $routes */

$routes->get('inscription',   'InscriptionController::index');
$routes->post('api/validate-step1', 'InscriptionController::validateStep1');
$routes->post('api/validate-step2', 'InscriptionController::validateStep2');
$routes->post('api/validate-step3', 'InscriptionController::validateStep3');
$routes->post('/api/complete-inscription', 'InscriptionController::completeInsc');

$routes->get('/',              'Home::index');
$routes->get('login',           'UserController::loginPage');
$routes->post('login',          'UserController::login');
$routes->get('inscription',     'UserController::inscription');
$routes->post('inscription',    'UserController::saveUser');

$routes->get('logout',          'UserController::logout');

// Admin login
$routes->get('admin',           'AdminController::index');
$routes->post('admin/tolog',    'AdminController::tolog');

// Gold option


// Code Promo
$routes->get('codepromo/form',          'CodePromoController::form');
$routes->post('codepromo/redeem',       'CodePromoController::redeem');
$routes->get('codepromo/mesDemandes',    'CodePromoController::mesDemandes');



$routes->group('user', ['filter' => 'user'], static function ($routes) {
	$routes->get('mes-regimes', 'UserController::mesRegimes');
	$routes->get('mes-regimes/export', 'UserController::exportMesRegimes');
	$routes->get('mes-regimes/(:num)', 'UserController::regimeDetails/$1');
	$routes->post('mes-regimes/souscrire/(:num)', 'UserController::souscrire/$1');
	$routes->get('dashboard', 'UserController::dashboard');
	$routes->get('profil',    'UserController::profil');
	$routes->get('gold/activate',   'DemandeGoldController::activate');

});
// Admin area protected by filter
$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
	$routes->get('dashboard', 'AdminController::dashboard');
	$routes->get('logout', 'AdminController::logout');
	$routes->get('codepromo/create',  'CodePromoController::adminForm');
	$routes->post('codepromo/create', 'CodePromoController::adminCreate');
	$routes->get('codepromo/demandes',   'CodePromoController::adminDemandes');
	$routes->get('codepromo/list',    'CodePromoController::adminList');
	$routes->post('codepromo/valider', 'CodePromoController::adminValider');
	$routes->post('codepromo/rejeter',  'CodePromoController::adminRejeter');
	$routes->get('notifications',          'NotificationController::index');

	// gold
	$routes->get('gold/demandes',          'DemandeGoldController::adminDemandes');
    $routes->post('gold/valider',          'DemandeGoldController::adminValider');
    $routes->post('gold/rejeter',          'DemandeGoldController::adminRejeter');
	$routes->get('gold/new',               'GoldController::adminNewIndex');
	$routes->add('gold/new',               'GoldController::adminNew');
	$routes->get('gold/debug-insert',      'GoldController::adminDebugInsert');
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

