<?php

/**
 * @package KN
 * @subpackage KN Exception Handler
 */

declare(strict_types=1);

namespace KN\Core;
use KN\Helpers\Base;

final class Exception {

	/**
	 *  Fatal error handler
	 **/
	static function fatalHandler() {
		
		$error = error_get_last();
		if ( ! is_null($error) AND is_array($error) AND $error["type"] == E_ERROR ) {
			self::errorHandler( $error["type"], $error["message"], $error["file"], $error["line"] );
		}
	}

	/**
	 *  Error handler output
	 **/
	static function errorHandler($errNo, string $errMsg, string $file, int $line) {
		
		ob_get_clean();
		ob_start();

		$output = '';
		if (! Base::config('app.dev_mode')) {
			$output .= $file.':'.$line.' - ';
		}
		$output .= $errMsg;
		if ($errNo) {
			$output .= ' <strong>('.$errNo.')</strong>';
		}

		if (isset($_SERVER['HTTP_ACCEPT']) !== false AND $_SERVER['HTTP_ACCEPT'] == 'application/json') {

			Base::http('content_type', [
				'content' => 'json',
				'write' => json_encode([
					'alerts' => '<div class="kn-alert kn-alert-danger">' . $output . '</div>'
				])
			]);

		} else {

			$handlerInterface = '
			<!doctype html>
			<html>
				<head>
					<meta charset="utf-8">
					<title>Error Handler - KN</title>
					<style>
					body {
					  font-family: ui-monospace, 
						 Menlo, Monaco, 
						 "Cascadia Mono", "Segoe UI Mono", 
						 "Roboto Mono", 
						 "Oxygen Mono", 
						 "Ubuntu Monospace", 
						 "Source Code Pro",
						 "Fira Mono", 
						 "Droid Sans Mono", 
						 "Courier New", monospace;
					  background: #151515;
					  color: #b2b2b2;
					  padding: 1rem;
					}
					h1 {
						margin: 0;
						color: #bebebe;
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

			$errorOutput = '    '.$output;
			// if (! KN:config('app.dev_mode')) http(500);
			echo str_replace('[OUTPUT]', $errorOutput, $handlerInterface);
		}
		exit;
	}

	/**
	 *  Exception handler
	 **/
	static function exceptionHandler($e = null) {

		if ( is_null($e) ) {

			die('Not handledable.');

		} else {

			self::errorHandler($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
		}
	}

}