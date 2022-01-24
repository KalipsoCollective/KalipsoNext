<?php

$route = new App\Core\Route();


$route->addRoutes([

	// format = class@method => [arg1, arg2, ...]

	// WEB
	'/'	=> [
		'middlewares'	=> [],
		'controller'	=> 'AppController@index'
	],
	'/account'	=> [
		'middlewares'	=> ['Auth@with' => ['auth']],
		'controller'	=> 'UserController@account'
	],
	'/account/login'	=> [
		'middlewares'	=> ['Auth@with' => ['nonAuth'], 'CSRF@validate' => ['POST']],
		'controller'	=> 'UserController@login'
	],
	'/account/register'	=> [
		'middlewares'	=> ['Auth@with' => ['nonAuth']],
		'controller'	=> 'UserController@register'
	],
	'/account/recovery'	=> [
		'middlewares'	=> ['Auth@with' => ['nonAuth']],
		'controller'	=> 'UserController@recovery'
	],

	/*! Base Routes - please don't remove! */
	'/script'	=> [
		'controller'	=> 'AppController@dynamicJS'
	],
	'/sandbox'	=> [
		'controller'	=> 'AppController@sandbox'
	],
	'/sandbox/:action'	=> [
		'controller'	=> 'AppController@sandbox'
	],


	// API
	'/api'	=> [
		'middlewares'	=> [],
		'controller'	=> 'ApiController@index',
		'function'		=> function() {
			echo '<pre>API Index</pre>';
		}
	],

]);


$route->addRoute('/example');

return $route;