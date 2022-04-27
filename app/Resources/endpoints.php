<?php

/**
 * We use this file to specify authorization points and language definitions for display.
 * ex: [endpoint] => ['default' => (checked status), 'name' => (language definition for display)]
 */

return [
	'auth' => [
		'default' => true,
		'name' => 'auth.auth',
	],
	'auth/:action' => [
		'default' => true,
		'name' => 'auth.auth_action',
	],
	'auth/logout' => [
		'default' => true,
		'name' => 'auth.auth_logout',
	],
	'management' => [
		'default' => false,
		'name' => 'auth.management',
	],
	'management/users' => [
		'default' => false,
		'name' => 'auth.management_users',
	],
	'management/roles' => [
		'default' => false,
		'name' => 'auth.management_roles',
	],
	'management/sessions' => [
		'default' => false,
		'name' => 'auth.management_sessions',
	],
	'management/settings' => [
		'default' => false,
		'name' => 'auth.management_settings',
	],
	'management/logs' => [
		'default' => false,
		'name' => 'auth.management_logs',
	],
];