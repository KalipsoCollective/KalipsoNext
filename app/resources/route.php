<?php

$route = new App\Core\Route();


$route->addRoutes([

	// WEB
	'/'	=> [
		'middlewares'	=> [],
		'controller'	=> ['AppController::index']
	],
	'/account'	=> [
		'middlewares'	=> [['Auth::with', ['auth']]],
		'controller'	=> ['AppController::account']
	],
	'/account/login'	=> [
		'middlewares'	=> [['Auth::with', ['nonAuth']]],
		'controller'	=> ['AppController::login']
	],
	'/account/register'	=> [
		'middlewares'	=> [['Auth::with', ['nonAuth']]],
		'controller'	=> ['AppController::register']
	],
	'/blogs'	=> [
		'controller'	=> ['BlogController::list']
	],
	'/blogs/:slug'	=> [
		'controller'	=> ['BlogController::single']
	],


	// API
	'/api'	=> [
		'middlewares'	=> [],
		'controller'	=> ['ApiController::index'],
		'function'		=> function() {
			echo '<pre>API Index</pre>';
		}
	],

]);


$route->addRoute('/example');

return $route;