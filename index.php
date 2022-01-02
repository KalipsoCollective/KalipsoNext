<?php 

/**
 * @package KN
 * @author halillusion <halillusion@gmail.com>
 **/

declare(strict_types=1);

try {
    
    require __DIR__.'/vendor/autoload.php';
    require __DIR__.'/app/bootstrap.php';
    
    App\Core\System::start();

} catch (Exception $e) {

    App\Core\Exception::exceptionHandler($e);

}