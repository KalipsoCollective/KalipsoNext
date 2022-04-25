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
            ['GET,POST', '/settings', 'UserController@settings', ['Auth@with', 'CSRF@validate']],
            ['GET,POST', '/sessions', 'UserController@sessions', ['Auth@with']],
        ];
    });

    $app->routeGroup(['GET,POST', '/management', 'AdminController@dashboard', ['Auth@with']], function () {
        return [
            ['GET,POST', '/users', 'AdminController@users', ['Auth@withOut']],
            ['GET,POST', '/roles', 'AdminController@userRoles', ['Auth@withOut']],
            ['GET,POST', '/sessions', 'AdminController@sessions', ['Auth@with']],
            ['GET,POST', '/contents', 'AdminController@contents', ['Auth@with']],
            ['GET,POST', '/files', 'AdminController@files', ['Auth@with']],
            ['GET,POST', '/settings', 'AdminController@settings', ['Auth@withOut']],
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