<?php

/**
 * @package KN
 * @subpackage KN System
 */

declare(strict_types=1);

namespace KN\Core;

use KN\Helpers\Base;
use KN\Core\Log;

final class Factory 
{
    /**
     *  Alert types 
     **/
    const ALERT_ERROR = 'error';
    const ALERT_WARNING = 'warning';
    const ALERT_SUCCESS = 'success';
    const ALERT_INFO = 'info';
    const ALERT_DEFAULT = 'default';

    /**
     * All request details as object
     **/
    public $request;
    public $auth = false;
    public $response;
    public $routes = [];
    public $lang = '';
    public $log = true;

    /**
     *  
     * Request handler 
     **/

    public function __construct() 
    {

        global $languageFile;

        /**
         * Assign default language 
         **/
        $this->lang = Base::config('app.default_language');

        /**
         * 
         * X_POWERED_BY header - please don't remove! 
         **/

        Base::http('powered_by');

        /**
         * 
         * KN_SESSION_NAME definition for next actions
         **/

        define('KN_SESSION_NAME', Base::config('app.session'));


        /**
         * 
         * Start session and output buffering
         **/
        Base::sessionStart(); // It is created using KN_SESSION_NAME
        ob_start();


        /**
         *  Auth check 
         **/
        $this->authCheck();

        /**
         * 
         *  Handle request and method
         **/

        $this->response = (object)[
            'statusCode'    => 200,
            'status'        => true,
            'data'          => [],
            'alerts'        => [],
            'redirect'      => [], // link, second, http status code
            'view'          => [] // view parameters -> [0] = page, [1] = layout
        ];
        $this->request = (object)[];

        $url = parse_url($_SERVER['REQUEST_URI']);
        $this->request->uri = '/' . trim(
            $url['path'] === '/' ? $url['path'] : rtrim($url['path'], '/'), '/'
        );
        $this->request->method = strtoupper(
            empty($_SERVER['REQUEST_METHOD']) ? 'GET' : $_SERVER['REQUEST_METHOD']
        );

        /**
         * Clean GET parameters
         **/ 
        $langChanged = false;
        if (isset($_GET) !== false AND count($_GET)) {

            foreach ($_GET as $key => $value) {
                $this->request->params[$key] = Base::filter($value);

                if ($key === 'lang' AND in_array($value, Base::config('app.available_languages'))) {
                    $this->lang = $value;
                    $langChanged = true;
                }
            }
        }


        /**
         * Clean POST parameters
         **/ 

        if (isset($_POST) !== false AND count($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->request->params[$key] = Base::filter($value);
            }
        }

        /**
         * 
         * Language definition 
         **/
        $sessionLanguageParam = Base::getSession('language');
        if (
            $langChanged AND 
            $sessionLanguageParam != $this->lang AND 
            file_exists($path = Base::path('app/Resources/localization/'.$this->lang.'.php'))
        ) {

            $languageFile = require $path;
            Base::setSession($this->lang, 'language');

        } elseif (
            is_null($sessionLanguageParam) AND 
            file_exists($path = Base::path('app/Resources/localization/'.$this->lang.'.php'))

        ) {

            $languageFile = require $path;
            Base::setSession($this->lang, 'language');
            
        } elseif (
            ! is_null($sessionLanguageParam) AND 
            file_exists($path = Base::path('app/Resources/localization/'.$sessionLanguageParam.'.php'))

        ) {

            $languageFile = require $path;
            $this->lang = $sessionLanguageParam;
            
        } else {
            throw new \Exception("Language file is not found!");
        }

    }


    /**
     *  Router register 
     *  @param string method        available method or methods(with comma)
     *  @param string route         link definition
     *  @param string controller    controller definition, ex: (AppController@index)
     *  @param array  middlewares   middleware definition, ex: ['CSRF@validate', 'Auth@with']
     *  @return this
     **/

    public function route($method, $route, $controller, $middlewares = [])
    {
        $methods = strpos($method, ',') ? explode(',', $method) : [$method];

        foreach ($methods as $method) {
            $detail = [
                'controller' => $controller, 
                'middlewares' => $middlewares
            ];

            if (! count($detail['middlewares'])) {
                unset($detail['middlewares']);
            }

            $this->routes[$route][$method] = $detail;
        }


        return $this;

    }


    /**
     *  Sub router register 
     *  @param array root        available method or methods(with comma)
     *  @param string method        available method or methods(with comma)
     *  @param string route         link definition
     *  @param string controller    controller definition, ex: (AppController@index)
     *  @param array  middlewares   middleware definition, ex: ['CSRF@validate', 'Auth@with']
     *  @return this
     **/

    public function routeWithRoot($root, $method, $route, $controller, $middlewares = [])
    {
        $methods = strpos($method, ',') ? explode(',', $method) : [$method];

        foreach ($methods as $method) {
            $detail = [
                'controller' => $controller, 
                'middlewares' => $middlewares
            ];

            if (! count($detail['middlewares'])) {
                unset($detail['middlewares']);
            }

            $this->routes[$root[1].$route][$method] = $detail;
        }


        return $this;

    }


    /**
     * Multi route register 
     * @param routes -> multi route definition as array
     * @return this
     **/
    public function routes($routes) {

        foreach ($routes as $route)
            $this->route(...$route);

        return $this;
    }


    /**
     * Root-bound groupped route register
     * @param array root            root route definition
     * @param function subRoutes    sub route definitions
     * @return this
     **/
    public function routeGroup($root, $subRoutes) {

        // register root route
        $this->route(...$root);

        foreach ($subRoutes() as $route)
            $this->routeWithRoot($root, ...$route);

        return $this;
    }


    /**
     * 
     * App starter
     * @return this
     **/

    public function run() {

        $notFound = true;
        /**
         *
         * Method step 
         **/
        $route = isset($this->routes[$this->request->uri]) !== false ?
            $this->routes[$this->request->uri] : null;

        
        /**
         * Parse request 
         **/

        if (is_null($route)) {

            foreach ($this->routes as $path => $details) {

                /**
                 *
                 * Catch attributes
                 **/

                if (strpos($path, ':') !== false) {

                    $explodedPath = trim($path, '/'); 
                    $explodedRequest = trim($this->request->uri, '/');

                    $explodedPath = strpos($explodedPath, '/') !== false ? 
                        explode('/', $explodedPath) : [$explodedPath];

                    $explodedRequest = strpos($explodedRequest, '/') !== false ? 
                        explode('/', $explodedRequest) : [$explodedRequest];


                    /**
                     * when the format equal 
                     **/
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

                                // extract as attribute
                                $this->request->attributes[ltrim($pathBody, ':')] = 
                                    $explodedRequest[$pathIndex];

                                $route = $details;
                                $notFound = false;

                            } elseif ($pathBody == $explodedRequest[$pathIndex]) { // direct directory check

                                $route = $details;
                                $notFound = false;

                            } else { // Undefined

                                break;

                            }
                        }
                    }
                }
            }

        } else {

            $notFound = false;

        }

        // 404
        if ($notFound) {

            $this->response->statusCode = 404;
            $this->response->title = Base::lang('err');
            $this->response->arguments = [
                'error' => '404',
                'output' => Base::lang('error.page_not_found')
            ];

            // Output
            $this->response();

        } else {

            if (isset($route[$this->request->method]) !== false) {

                $route = $route[$this->request->method];

                /**
                 * 
                 * Middleware step
                 **/

                $next = true;

                if (isset($route['middlewares']) !== false) {

                    foreach ($route['middlewares'] as $middleware) {

                        $middleware = explode('@', $middleware, 2);

                        $method = $middleware[1];
                        $class = 'KN\\Middlewares\\' . $middleware[0];

                        $middleware = (new $class(
                            $this
                        ))->$method();

                        /**
                         * Middleware alerts 
                         **/
                        if (isset($middleware['alerts']) !== false)
                            $this->response->alerts = array_merge(
                                $this->response->alerts, 
                                $middleware['alerts']
                            );

                        /**
                         *  If we have alerts, we will display them on the next page with the session.
                         **/
                        if (isset($middleware['redirect']) !== false)
                            $this->response->redirect = $middleware['redirect'];


                        /**
                         * Arguments 
                         **/
                        if (isset($middleware['arguments']) !== false)
                            $this->response->arguments = $middleware['arguments'];

                        /**
                         * Change status code if middleware returns.
                         **/
                        if (isset($middleware['statusCode']) !== false)
                            $this->response->statusCode = $middleware['statusCode'];

                        /**
                         * A status token to use in some conditions, such as API responses. It must be boolean.
                         **/
                        $this->response->status = $middleware['status'];

                        if (! $middleware['next'])
                            $next = false;

                        if (! $middleware['status'])
                            break;
                    }

                }

                /**
                 * 
                 * Controller step
                 **/

                if ($next) {

                    if (isset($route['controller']) !== false) {

                        $controller = explode('@', $route['controller'], 2);

                        $method = $controller[1];
                        $class = 'KN\\Controllers\\' . $controller[0];

                        $controller = (new $class(
                            $this
                        ))->$method();

                        /**
                         * Middleware alerts 
                         **/
                        if (isset($controller['alerts']) !== false)
                            $this->response->alerts = array_merge(
                                $this->response->alerts, 
                                $controller['alerts']
                            );

                        /**
                         *  If we have alerts, we will display them on the next page with the session.
                         **/
                        if (isset($controller['redirect']) !== false)
                            $this->response->redirect = $controller['redirect'];


                        /**
                         * Arguments 
                         **/
                        if (isset($controller['arguments']) !== false)
                            $this->response->arguments = $controller['arguments'];

                        /**
                         * Log 
                         **/
                        if (isset($controller['log']) !== false)
                            $this->log = $controller['log'];

                        /**
                         * Change status code if middleware returns.
                         **/
                        if (isset($controller['statusCode']) !== false)
                            $this->response->statusCode = $controller['statusCode'];

                        /**
                         * A status token to use in some conditions, such as API responses. It must be boolean.
                         **/
                        $this->response->status = $controller['status'];

                        if (isset($controller['view']) !== false)
                            $this->response->view = $controller['view'];

                    } else {

                        throw new \Exception(Base::lang('error.controller_not_defined'));
                    }

                }

                // Output
                $this->response();

            } else { // 405

                $this->response->statusCode = 405;
                $this->response->title = Base::lang('err');
                $this->response->arguments = [
                    'error' => '405',
                    'output' => Base::lang('error.method_not_allowed')
                ];

            }

        }

        return $this;
    }


    /**
     * Extract created response
     * @return void
     **/
    public function response() {

        if ($this->response->statusCode === 200) {

            if ($this->response->view !== '') {

                if (is_string($this->response->view)) {
                    $viewFile = $this->response->view;
                    $viewLayout = 'app';
                } elseif (is_array($this->response->view) AND count($this->response->view) === 2) {
                    $viewFile = $this->response->view[0];
                    $viewLayout = $this->response->view[1];
                } else {
                    throw new \Exception(Base::lang('error.view_definition_not_found'));
                }
                
                Base::http($this->response->statusCode);
                $this->view($viewFile, 
                    $this->response->arguments, 
                    $viewLayout
                );

            }

        } else {

            if ($this->response->redirect) {

                Base::http($this->response->statusCode);
                Base::http('refresh', [
                    'url' => (is_array($this->response->redirect) ? $this->response->redirect[0] : $this->response->redirect),
                    'second' => (is_array($this->response->redirect) ? $this->response->redirect[1] : null)
                ]);

            } else {

                $this->view(
                    $this->response->statusCode, 
                    $this->response->arguments, 
                    'error'
                );

            }
        }
        
        if ($this->log AND Base::config('settings.log')) {
            (new Log())->add([
                'request'       => $this->request,
                'response'      => $this->response,
            ]);
        }
    }


    /**
     *
     * View Page 
     * @param string  file          view file name
     * @param array   arguments     needed view variables 
     * @param string  layout        page structure indicator
     * @return this
     **/

    public function view($file, $arguments = [], $layout = 'app') {


        /**
         * 
         * Send HTTP status code.
         **/

        Base::http($this->response->statusCode);


        /**
         * 
         * Arguments are extracted and the title is defined.
         **/

        $arguments['title'] = isset($arguments['title']) !== false ? 
            str_replace(
                ['[TITLE]', '[APP]'], 
                [$arguments['title'], 
                Base::config('settings.name')], Base::config('app.title_format')) 
                : Base::config('settings.name');

        extract($arguments);


        /**
         * 
         * Prepare the page structure according to the format.
         **/

        $layoutVars = Base::path('app/Resources/view/_layouts/_' . $layout . '.php');
        $layout = file_exists($layoutVars) ? (require $layoutVars) : ['_'];

        foreach ($layout as $part) {
            
            if ($part == '_')
                $part = strpos($file, '.') !== false ? str_replace('.', '/', $file) : $file;
            else
                $part = '_parts/' . $part;

            if (file_exists($req = Base::path('app/Resources/view/' . $part . '.php'))) {
                require $req;
            }

        }

    }


    /**
     * 
     *  Authority check with session
     *  @return this 
     **/
    public function authCheck() {

        if (isset($_SESSION['user']->id) !== false AND $_SESSION['user']->id)
            $this->auth = true;
        
        else
            $this->auth = false;

        return $this;
    }


    /**
     *  URL Generator
     *  @param string $route
     *  @return string $url
     **/
    public function url($route) {

        return $route;

    }


    /**
     * Returns the active class if the given link is the current link.
     * @param string $link     given link
     * @param string $class    html class to return
     * @param boolean $exact   it gives full return when it is exactly the same.
     * @return string $string
     **/
    public function currentLink($link, $class = 'active', $exact = true) {
        
        $return = '';
        if ($this->request->uri === $link OR 
            (! $exact AND strpos($this->request->uri, $link))
        ) {
            $return = ' ' . trim($class);
        }
        return $return;
    }
}