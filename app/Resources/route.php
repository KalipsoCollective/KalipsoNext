<?php

$route = new KN\Core\Route();


$route->addRoutes([

	// format = class@method => [arg1, arg2, ...]

	// WEB
	'/'	=> [
		'middlewares'	=> [],
		'controller'	=> 'AppController@index'
	],
	'/account'	=> [
		'middlewares'	=> ['Auth@verify' => [], 'Auth@with' => ['auth']],
		'controller'	=> 'UserController@account'
	],
	'/account/profile'	=> [
		'middlewares'	=> ['Auth@with' => ['auth'], 'CSRF@validate' => ['POST']],
		'controller'	=> 'UserController@account'
	],
	'/account/sessions'	=> [
		'middlewares'	=> ['Auth@with' => ['auth']],
		'controller'	=> 'UserController@account'
	],
	'/account'	=> [
		'middlewares'	=> ['Auth@verify' => [], 'Auth@with' => ['auth']],
		'controller'	=> 'UserController@account'
	],
	'/account/login'	=> [
		'middlewares'	=> ['Auth@with' => ['nonAuth'], 'CSRF@validate' => ['POST']],
		'controller'	=> 'UserController@login'
	],
	'/account/register'	=> [
		'middlewares'	=> ['Auth@with' => ['nonAuth'], 'CSRF@validate' => ['POST']],
		'controller'	=> 'UserController@register'
	],
	'/account/recovery'	=> [
		'middlewares'	=> ['Auth@with' => ['nonAuth']],
		'controller'	=> 'UserController@recovery'
	],

	/*! Base Routes - please don't remove! */
	'/script'	=> [
		'controller'	=> 'AppController@dynamicJS',
		'session_check' => false,
		'log'			=> false,
	],
	'/sandbox'	=> [
		'controller'	=> 'AppController@sandbox',
		'session_check' => false,
		'log'			=> false,
	],
	'/sandbox/:action'	=> [
		'controller'	=> 'AppController@sandbox',
		'session_check' => false,
		'log'			=> false,
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