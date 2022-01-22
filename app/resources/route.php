<?php

$route = new App\Core\Route();


$route->addRoutes([

	// WEB
	'/'	=> [
		'middlewares'	=> [],
		'controller'	=> 'AppController::index'
	],
	'/account'	=> [
		'middlewares'	=> [['Auth::with', ['auth']]],
		'controller'	=> 'UserController::account'
	],
	'/account/login'	=> [
		'middlewares'	=> [['Auth::with', ['nonAuth']]],
		'controller'	=> 'UserController::login'
	],
	'/account/register'	=> [
		'middlewares'	=> [['Auth::with', ['nonAuth']]],
		'controller'	=> 'UserController::register'
	],
	'/blogs'	=> [
		'controller'	=> 'BlogController::list'
	],
	'/blogs/:slug'	=> [
		'controller'	=> 'BlogController::single'
	],
	'/blogs/:slug/comments/:commentId'	=> [
		'controller'	=> 'BlogController::comments'
	],

	/*! Base Routes */
	'/script'	=> [
		'controller'	=> 'AppController::dynamicJS'
	],
	'/sandbox'	=> [
		'controller'	=> 'AppController::sandbox'
	],
	'/sandbox/:action'	=> [
		'controller'	=> 'AppController::sandbox'
	],


	// API
	'/api'	=> [
		'middlewares'	=> [],
		'controller'	=> 'ApiController::index',
		'function'		=> function() {
			echo '<pre>API Index</pre>';
		}
	],

]);


$route->addRoute('/example');

return $route;