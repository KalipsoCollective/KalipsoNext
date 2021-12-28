<?php

use App\Core;

// Handler
register_shutdown_function( function() {
    App\Core\Exception::fatalHandler();
});
set_error_handler( function($level, $error, $file, $line) {
	if(0 === error_reporting()){
        return false;
    }
    App\Core\Exception::errorHandler($level, $error, $file, $line);
}, E_ALL);
set_exception_handler( function($e) {
    App\Core\Exception::exceptionHandler($e);
});
ini_set('display_errors', 'on');
error_reporting(E_ALL);