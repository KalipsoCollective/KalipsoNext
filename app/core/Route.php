<?php

/**
 * @package KN
 * @subpackage KN Exception Handler
 */

declare(strict_types=1);

namespace KN\Core;

use KN\Helpers\Base;
use KN\Controllers\UserController;
use KN\Core\Log;

class Route {

    private static $schema = null;
    private static $request = null;
    private static $requestMethod = null;
    private static $params = [];
    private static $attributes = [];
    private static $pathNotFound = null;
    private static $methodNotAllowed = null;
    private static $matchingRoute = null;
    private static $status = 200;

    public function __construct() {

        global $requestUri;

        $url = parse_url($_SERVER['REQUEST_URI']);
        self::$request = $url['path'] === '/' ? $url['path'] : rtrim($url['path'], '/');
        self::$requestMethod = strtoupper(empty($_SERVER['REQUEST_METHOD']) ? 'GET' : $_SERVER['REQUEST_METHOD']);

        $requestUri = trim(self::$request, '/');

        // GET Parameters
        if (isset($_GET) !== false AND count($_GET)) {
            foreach ($_GET as $key => $value) {
                self::$params[$key] = Base::filter($value);
            }
        }

        // POST Parameters
        if (isset($_POST) !== false AND count($_POST)) {
            foreach ($_POST as $key => $value) {
                self::$params[$key] = Base::filter($value);
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

            self::addRoute($path, $properties);

        }
    }


    /**
    * Method used to add a new route
    * @param string $path    Route expressions
    * @param array $properties    Route properties
    *
    */
    public static function addRoute(string $path, array $properties = []) {

        self::$schema[$path] = [
            'middlewares'   => isset($properties['middlewares']) !== false ? 
                $properties['middlewares'] : [],
            'controller'    => isset($properties['controller']) !== false ? 
                $properties['controller'] : [],
            'function'      => isset($properties['function']) !== false ? 
                $properties['function'] : null,
            'session_check' => isset($properties['session_check']) !== false ? 
                $properties['session_check'] : true,
            'log'           => isset($properties['log']) !== false ? 
                $properties['log'] : true,
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

                    $explodedPath = strpos($explodedPath, '/') !== false ? 
                        explode('/', $explodedPath) : [$explodedPath];

                    $explodedRequest = strpos($explodedRequest, '/') !== false ? 
                        explode('/', $explodedRequest) : [$explodedRequest];

                    if (count($explodedPath) === count($explodedRequest)) {

                        preg_match_all(
                            '@(:([a-zA-Z0-9_-]+))@m', 
                            $path, 
                            $expMatches, 
                            PREG_SET_ORDER, 
                            0
                        );

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

        $request = [
            'request'           => self::$request,
            'request_method'    => self::$requestMethod,
            'parameters'        => self::$params,
            'attributes'        => self::$attributes
        ];
        $addLog = true;

        if (self::$pathNotFound) {

            self::$status = 404;
            Base::http(self::$status);
            $messages[] = [
                'status' => 'alert',
                'title'  => Base::lang('alert'),
                'message'=> Base::lang('page_not_found')
            ];

            Base::layout('404', [
                'title'     => Base::lang('page_not_found') . ' | ' . Base::config('app.name'),
                'request'   => $request,
                'response'  => ['messages' => $messages]
            ]);

            (new UserController($request))->checkSession();

        } else {

            self::$matchingRoute = self::$schema[$index];
            $addLog = self::$matchingRoute['log'];

            $middlewareMessages = [];

            if (self::$matchingRoute['session_check']) {
                (new UserController($request))->checkSession();
            }

            // middleware
            $middlewareError = false;

            if (count(self::$matchingRoute['middlewares'])) {

                foreach (self::$matchingRoute['middlewares'] as $class => $arguments) {

                    try {
                        // call class and method
                        if (strpos($class, '@') !== false) {

                            $class = explode('@', $class, 2);
                            $method = $class[1];
                            $class = 'KN\\Middlewares\\' . $class[0];

                            $middleware = (new $class(
                                $request
                            ))->$method(...$arguments);

                        } else { // call class with construct

                            $class = 'KN\\Middlewares\\' . $class;
                            $middleware = (new $class(
                                $request, 
                                ...$arguments
                            ));

                        }

                        if (isset($middleware['message']) !== false) {
                            $middlewareMessages[] = $middleware['message'];
                        }

                        if (! $middleware['status']) {
                            $middlewareError = true;
                        }
                        

                    } catch (Exception $e) {
                        throw $e;
                    }
                }
            }

            if (! $middlewareError) {

                // controller
                if (isset(self::$matchingRoute['controller']) !== false) {


                    $class = self::$matchingRoute['controller'];

                    if (count($middlewareMessages)) {
                        $request['middleware_messages'] = $middlewareMessages;
                    }

                    try {

                        // call class and method
                        if (strpos($class, '@') !== false) {

                            $class = explode('@', $class, 2);
                            $method = $class[1];
                            $class = 'KN\\Controllers\\' . $class[0];

                            $controller = (new $class(
                                $request
                            ))->$method();

                        } else { // call class with construct

                            $class = 'KN\\Controllers\\' . $class;
                            $controller = (new $class(
                                $request
                            ));
                        }

                        if ($controller)
                            $response = $controller;
                        

                    } catch (Exception $e) {

                        throw $e;
                    }
                }

            } else {

                self::$status = 401;

                foreach ($middlewareMessages as $message) {

                    
                    if (is_array($message)) {

                        $status = $message['status'];
                        $title = Base::lang($message['title']);
                        $message = Base::lang($message['message']);
                        if (isset($message['link'])) 
                            $link = $message['link'];

                    } else {

                        $status = 'default';
                        $title = Base::lang('alert');
                        $message = Base::lang($message);
                        $link = [Base::lang('go_to_home'), Base::base()];
                    }

                    $m = [
                        'status' => $status,
                        'title'  => $title,
                        'message'=> $message,
                        'close'  => false
                    ];

                    if (isset($link))
                        $m['link'] = $link;

                    $messages[] = $m;

                }
                $response = ['messages' => $messages];

                Base::layout(self::$status, [
                    'title'     => Base::lang('a_problem_occurred') . ' | ' . Base::config('app.name'),
                    'request'   => $request,
                    'response'  => $response
                ]);
            }

            
        }

        if ($addLog) {
            (new Log())->add([
                'request'       => $request,
                'http_status'   => self::$status,
                'response'      => isset($response) !== false ? $response : null
            ]);
        }

    }

}