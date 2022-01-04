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
    private static $pathNotFound = null;
    private static $methodNotAllowed = null;

    public function __construct() {

        $url = parse_url($_SERVER['REQUEST_URI']);
        self::$request = $url['path'] === '/' ? $url['path'] : rtrim($url['path'], '/');
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


    /**
    * Method used to add a new route
    * @param string $path    Route expressions
    * @param array $properties    Route properties
    * @param string $method    Route method
    *
    */
    public static function addRoute(string $path, array $properties = [], string $method = 'get') {

        self::$schema[$path] = [
            'middlewares'   => isset($properties['middlewares']) !== false ? $properties['middlewares'] : [],
            'controller'    => isset($properties['controller']) !== false ? $properties['controller'] : [],
            'function'      => isset($properties['function']) !== false ? $properties['function'] : null,
            'method'        => strtolower(isset($method) !== false ? $method : 'GET'),  
        ];

    }


    public static function run() {

        if ( isset( self::$schema[self::$request] ) !== false ) { // directly compatible expression

            // Middleware

            if ( self::$schema[self::$request]['function'] ) {
                self::$schema[self::$request]['function']();
            } else {
                KN::dump(self::$schema[self::$request]);
            }

            
        } else {

            foreach(self::$schema as $path => $properties) {
                
                preg_match_all('@(:([a-zA-Z0-9_-]+))@m', $path, $m, PREG_SET_ORDER, 0);
                KN::dump($m);
                KN::dump($path);
                KN::dump(self::$request);
                echo '<br>----<br>';

            }

        }

        // KN::dump(self::$schema);
        // KN::dump(self::$request);

    }

}