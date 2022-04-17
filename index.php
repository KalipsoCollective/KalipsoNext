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

    // Route group
    $app->routes([
        ['GET,POST', '/auth/login', 'UserController@login', ['Auth@withOut']],
        ['GET,POST', '/auth/register', 'UserController@register', ['Auth@withOut']],
        ['GET,POST', '/auth/recovery', 'UserController@recovery', ['Auth@withOut']],
        ['GET,POST', '/auth', 'UserController@account', ['Auth@with']]
    ]);

    // Single route
    $app->route('GET', '/', 'AppController@index');

    // Do not remove this route for the KN script library.
    $app->route('GET,POST', '/script', 'AppController@script');

    $app->run();

} catch (Exception $e) {

    KN\Core\Exception::exceptionHandler($e);

}