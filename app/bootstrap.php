<?php

// register_shutdown_function
register_shutdown_function( function() {
    KN\Core\Exception::fatalHandler();
});

// set_error_handler
set_error_handler( function($level, $error, $file, $line) {
	if (0 === error_reporting()) {
        return false;
    }
    KN\Core\Exception::errorHandler($level, $error, $file, $line);
}, E_ALL);

// set_exception_handler
set_exception_handler( function($e) {
    KN\Core\Exception::exceptionHandler($e);
});

ini_set('display_errors', 'on');
error_reporting(E_ALL);

// basic defines
define('KN_START', microtime(true));
define('KN_ROOT',  rtrim($_SERVER["DOCUMENT_ROOT"], '/').'/');
define('KN_VERSION', '1.0.0');