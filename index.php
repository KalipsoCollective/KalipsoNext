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

    $app->routes([
        ['GET,POST', '/login', 'UserController@login', ['Auth@withOut']],
        ['GET,POST', '/register', 'UserController@register', ['Auth@withOut']],
        ['GET,POST', '/recovery', 'UserController@recovery', ['Auth@withOut']]
    ]);

    $app->route('POST', '/', 'AppController@index');

    // Do not remove this route for the KN script library.
    $app->route('POST', '/script', 'AppController@script');

    $app->run();

} catch (Exception $e) {

    KN\Core\Exception::exceptionHandler($e);

}