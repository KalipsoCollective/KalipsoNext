<?php 

/**
 * @package KN
 * @author halillusion <halillusion@gmail.com>
 **/

declare(strict_types=1);

// Basic defines
define('KN_START', microtime(true));
define('KN_ROOT',  rtrim($_SERVER["DOCUMENT_ROOT"], '/').'/');


error_reporting(E_ALL);

try {
    
    require __DIR__.'/vendor/autoload.php';
    require __DIR__.'/app/bootstrap.php';
    start();

} catch (Exception $e) {

    App\Core\Exception::exceptionHandler($e);

}