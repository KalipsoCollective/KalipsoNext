<?php

$route = new App\Core\Route();


$route->addRoutes([

	// WEB
	'/'	=> [
		'middlewares'	=> [],
		'controller'	=> ['AppController::index']
	],
	'/login'	=> [
		'middlewares'	=> [['Auth::with', ['nonAuth']]],
		'controller'	=> ['AppController::login']
	],


	// API
	'/api'	=> [
		'middlewares'	=> [],
		'controller'	=> ['ApiController::index']
	],

]);

/*
$route->addRoute('/example', [])
*/

return $route;