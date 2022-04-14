<?php 

/**
 * @package KN
 * @author halillusion <halillusion@gmail.com>
 **/

declare(strict_types=1);

try {
    
    require __DIR__.'/vendor/autoload.php';
    require __DIR__.'/app/bootstrap.php';

    (new KN\Core\System)->go();
    // routing -> resources/route.php

} catch (Exception $e) {

    KN\Core\Exception::exceptionHandler($e);

}