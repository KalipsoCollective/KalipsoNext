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

        global $requestUri;

        $url = parse_url($_SERVER['REQUEST_URI']);
        self::$request = $url['path'] === '/' ? $url['path'] : rtrim($url['path'], '/');
        self::$requestMethod = strtoupper(empty($_SERVER['REQUEST_METHOD']) ? 'GET' : $_SERVER['REQUEST_METHOD']);

        $requestUri = trim(self::$request, '/');

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

            $request = [
                'request'           => self::$request,
                'request_method'    => self::$requestMethod,
                'parameters'        => self::$params,
                'attributes'        => self::$attributes
            ];

            $middlewareMessages = [];
            // middleware
            if (count(self::$matchingRoute['middlewares'])) {


                foreach (self::$matchingRoute['middlewares'] as $class => $arguments) {

                    try {

                        // call class and method
                        if (strpos($class, '@') !== false) {

                            $class = explode('@', $class, 2);
                            $method = $class[1];
                            $class = 'App\\Middlewares\\' . $class[0];

                            $middleware = (new $class(
                                $request
                            ))->$method(...$arguments);

                        } else { // call class with construct

                            $class = 'App\\Middlewares\\' . $class;
                            $middleware = (new $class(
                                $request, 
                                ...$arguments
                            ));

                        }

                        if (! $middleware['status']) {
                            $middlewareMessages[] = $middleware['message'];
                            break;
                        }
                        

                    } catch (Exception $e) {
                        throw $e;
                    }
                }
            }


            if (! count($middlewareMessages)) {

                // controller
                if (isset(self::$matchingRoute['controller']) !== false) {


                    $class = self::$matchingRoute['controller'];

                    try {

                        // call class and method
                        if (strpos($class, '@') !== false) {

                            $class = explode('@', $class, 2);
                            $method = $class[1];
                            $class = 'App\\Controllers\\' . $class[0];

                            $middleware = (new $class(
                                $request
                            ))->$method(...$arguments);

                        } else { // call class with construct

                            $class = 'App\\Controllers\\' . $class;
                            $middleware = (new $class(
                                $request, 
                                ...$arguments
                            ));

                        }
                        

                    } catch (Exception $e) {
                        throw $e;
                    }
                }

            } else {

                KN::http(404);
                KN::view('404', ['messages' => $middlewareMessages]);
            }
        }

    }

}