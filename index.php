<?php 

/**
 * @package KN
 * @author halillusion <halillusion@gmail.com>
 **/

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/app/bootstrap.php';

try {

    $app = (new KN\Core\Factory);

    // Multi route group
    $app->routes([
        ['GET,POST', '/sandbox', 'AppController@sandbox'],
        ['GET,POST', '/sandbox/:action', 'AppController@sandbox']
    ]);


    // Root-bound route group
    $app->routeGroup(['GET,POST', '/auth', 'UserController@account', ['Auth@with']], function () {
        return [
            ['GET,POST', '/login', 'UserController@login', ['Auth@withOut', 'CSRF@validate']],
            ['GET,POST', '/register', 'UserController@register', ['Auth@withOut', 'CSRF@validate']],
            ['GET,POST', '/recovery', 'UserController@recovery', ['Auth@withOut', 'CSRF@validate']],
            ['GET,POST', '/logout', 'UserController@logout', ['Auth@with']],
            ['GET,POST', '/:action', 'UserController@account', ['Auth@with', 'CSRF@validate']],
        ];
    });

    $app->routeGroup(['GET,POST', '/management', 'AdminController@dashboard', ['Auth@with']], function () {
        return [
            // Users
            ['GET,POST', '/users', 'AdminController@users', ['Auth@with']],
            ['GET,POST', '/users/list', 'AdminController@userList', ['Auth@with']],
            ['GET,POST', '/users/add', 'AdminController@userAdd', ['Auth@with']],
            ['GET,POST', '/users/delete/:id', 'AdminController@userDelete', ['Auth@with']],
            ['GET,POST', '/users/update/:id', 'AdminController@userUpdate', ['Auth@with']],

            // Roles
            ['GET,POST', '/roles', 'AdminController@userRoles', ['Auth@with']],
            ['GET,POST', '/roles/list', 'AdminController@roleList', ['Auth@with']],
            ['GET,POST', '/roles/add', 'AdminController@roleAdd', ['Auth@with']],
            ['GET,POST', '/roles/delete/:id', 'AdminController@roleDelete', ['Auth@with']],
            ['GET,POST', '/roles/update/:id', 'AdminController@roleUpdate', ['Auth@with']],

            //
            ['GET,POST', '/sessions', 'AdminController@sessions', ['Auth@with']],
            ['GET,POST', '/settings', 'AdminController@settings', ['Auth@with']],
            ['GET,POST', '/logs', 'AdminController@logs', ['Auth@with']],
        ];
    });

    // Single route
    $app->route('GET', '/', 'AppController@index', ['Auth@verifyAccount']);

    // Do not remove this route for the KN script library.
    $app->route('GET,POST', '/script', 'AppController@script');
    $app->route('GET,POST', '/sandbox', 'AppController@sandbox');
    $app->route('GET,POST', '/sandbox/:action', 'AppController@sandbox');

    $app->run();

} catch (Exception $e) {

    KN\Core\Exception::exceptionHandler($e);

}