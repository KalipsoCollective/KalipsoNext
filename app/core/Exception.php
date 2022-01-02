<?php

/**
 * @package KN
 * @subpackage KN Exception Handler
 */

declare(strict_types=1);

namespace App\Core;

class Exception {

	static function fatalHandler() {
		
		$error = error_get_last();
	    if ( ! is_null($error) AND is_array($error) AND $error["type"] == E_ERROR ) {
	        self::errorHandler( $error["type"], $error["message"], $error["file"], $error["line"] );
	    }
	}

	static function errorHandler(int $errNo, string $errMsg, string $file, int $line) {
		
		$handlerInterface = '
        <!doctype html>
	        <html>
	            <head>
	                <meta charset="utf-8">
	                <title>Error Handler - KN</title>
	                <style>
	                body {
	                  font-family: monospace, system-ui;
	                  background: #151515;
	                  color: #b2b2b2;
	                  padding: 1rem;
	                }
	                h1 {
	                    margin: 0;
	                    color: #fff;
	                }
	                h2 {
	                    margin: 0;
	                    color: #777;
	                }
	                </style>
	            </head>
	            <body>
	                <h1>KalipsoNext</h1>
	                <h2>Error Handler</h2>
	                <pre>[OUTPUT]</pre>
	            </body>
	        </html>';

	    $errorOutput = '    '.$file.':'.$line.' - '.$errMsg.' <strong>('.$errNo.')</strong>';
	    // if (! config('app.dev_mode')) http(500);
	    echo str_replace('[OUTPUT]', $errorOutput, $handlerInterface);
	}

	static function exceptionHandler($e = null) {

		if ( is_null($e) ) {

			die('Not handledable.');

		} else {

			self::errorHandler($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
		}
	}

}