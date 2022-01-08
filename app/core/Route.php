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
    private static $attributes = [];
    private static $pathNotFound = null;
    private static $methodNotAllowed = null;
    private static $matchingRoute = null;

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


    /**
    * Method used to detect the route
    */
    public static function run() {

        $matchingSchemaIndex = null;
        if ( isset( self::$schema[self::$request] ) !== false ) { // directly compatible expression

            $matchingSchemaIndex = self::$request;

        } else { // dynamic compatible expression

            foreach(self::$schema as $path => $properties) {

                if (strpos($path, ':') !== false) {

                    $explodedPath = trim($path, '/'); 
                    $explodedRequest = trim(self::$request, '/');

                    $explodedPath = strpos($explodedPath, '/') !== false ? explode('/', $explodedPath) : [$explodedPath];
                    $explodedRequest = strpos($explodedRequest, '/') !== false ? explode('/', $explodedRequest) : [$explodedRequest];

                    if (count($explodedPath) === count($explodedRequest)) {

                        preg_match_all('@(:([a-zA-Z0-9_-]+))@m', $path, $expMatches, PREG_SET_ORDER, 0);

                        $expMatches = array_map( function($v) {
                            return $v[0];
                        }, $expMatches);

                        foreach ($explodedPath as $pathIndex => $pathBody) {

                            if (in_array($pathBody, $expMatches) !== false) { // slug directory check
                                self::$attributes[ltrim($pathBody, ':')] = $explodedRequest[$pathIndex];
                                $matchingSchemaIndex = $path;
                            } elseif ($pathBody == $explodedRequest[$pathIndex]) { // direct directory check
                                $matchingSchemaIndex = $path;
                            } else { // Undefined
                                $matchingSchemaIndex = null;
                                break;
                            }
                        }
                    }
                }
            }

        }

        self::$pathNotFound = is_null($matchingSchemaIndex) ? true : false;
        self::extractor($matchingSchemaIndex);

    }


    /**
    * Method used to extract the route
    * @param null|string $index    Related route key
    */
    public static function extractor($index = null) {

        if (self::$pathNotFound) {

            KN::http(404);
            KN::view('404');

        } else {

            self::$matchingRoute = self::$schema[$index];

            $return = null;

            // middleware
            if (count(self::$matchingRoute['middlewares'])) {


                foreach (self::$matchingRoute['middlewares'] as $middleware) {

                    $path = 'App\\Middlewares\\'.$middleware[0];

                    try {

                        $middleware = call_user_func_array(
                            $path, $middleware[1]
                        );

                        if (! $middleware['status']) {
                            $return = $middleware['message'];
                            break;
                        }
                        

                    } catch (Exception $e) {
                        throw 'middleware_not_found';
                    }
                }
            }


            if (is_null($return)) {

                // controller
                if (isset(self::$matchingRoute['controller']) !== false) {


                    $path = 'App\\Controllers\\'.self::$matchingRoute['controller'];

                    try {

                        call_user_func_array(
                            $path, []
                        );
                        

                    } catch (Exception $e) {
                        throw 'controller_not_found';
                    }
                }

            } else {

                KN::http(404);
                KN::view('404', ['message' => KN::lang($return)]);
            }
        }

    }

}