<?php

/**
 * @package KN
 * @subpackage KN Exception Handler
 */

declare(strict_types=1);

namespace App\Core;

use App\Helpers\KN;

class Route {

    private static $schema = null;
    private static $request = null;
    private static $requestMethod = null;
    private static $params = [];

    public function __construct() {

        $url = parse_url($_SERVER['REQUEST_URI']);
        self::$request = $url['path'];
        self::$requestMethod = strtoupper(empty($_SERVER['REQUEST_METHOD']) ? 'GET' : $_SERVER['REQUEST_METHOD']);

        // GET Parameters
        if (isset($_GET) !== false AND count($_GET)) {
            foreach ($_GET as $key => $value) {
                self::$params[$key] = KN::filter($value);
            }
        }

        // POST Parameters
        if (isset($_POST) !== false AND count($_POST)) {
            foreach ($_POST as $key => $value) {
                self::$params[$key] = KN::filter($value);
            }
        }

    }

    /**
    * Method used to add a new routes
    * @param array $schema    Route expressions
    *
    */
    public static function addRoutes(array $schema = []) {

        foreach ($schema as $path => $properties) {

            self::$schema[$path] = [
                'middlewares'   => isset($properties['middlewares']) !== false ? $properties['middlewares'] : [],
                'controller'    => isset($properties['controller']) !== false ? $properties['controller'] : [],
                'function'      => isset($properties['function']) !== false ? $properties['function'] : null,
                'method'        => strtolower(isset($properties['method']) !== false ? $properties['method'] : 'GET'),  
            ];

        }

    }


    public static function run() {

        KN::dump(self::$schema);
        KN::dump(self::$request);

    }

    // https://steampixel.de/simple-and-elegant-url-routing-with-php/

}