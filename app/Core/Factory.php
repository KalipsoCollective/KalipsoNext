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
    public $excludedRoutes = [];
    public $endpoints = [];
    public $endpoint;
    public $lang = '';
    public $log = true;
    public $action;

    /**
     *  
     * Request handler 
     **/

    public function __construct() 
    {

        global $languageFile;

        /**
         * Controller and middleware name for logs 
         */
        $this->action = (object)[
            'middleware' => [], 
            'controller' => ''
        ];

        /**
         * Assign default language 
         **/
        $this->lang = Base::config('settings.language');

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
         * 
         *  Handle request and method
         **/

        $this->response = (object)[
            'statusCode'    => 200,
            'status'        => true,
            'alerts'        => [],
            'redirect'      => [], // link, second, http status code
            'view'          => [] // as array: view parameters -> [0] = page, [1] = layout, as string: view_file, as json: null
        ];
        $this->request = (object)[];
        $this->request->params = [];
        $this->request->files = [];

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
         * Clean FILES parameters
         **/ 

        if (isset($_FILES) !== false AND count($_FILES)) {
            $files = [];
            foreach ($_FILES as $name => $data) {

                if (is_array($data['name'])) { // multiple upload
                    $files[$name] = [];
                    foreach ($data as $k => $l) {
                        foreach ($l as $i => $v) {
                            if (!array_key_exists($i, $files[$name]))
                                $files[$name][$i] = [];
                            $files[$name][$i][$k] = $v;
                        }
                    }
                } else { // single upload
                    $files[$name][] = $data;
                }
            }

            $this->request->files = $files;
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

        date_default_timezone_set(Base::lang('lang.timezone'));

        /**
         *  Auth check 
         **/
        if (strpos($this->request->uri, '/sandbox') !== false AND Base::config('app.dev_mode')) {
            $this->auth = true;
        } else {
            $this->authCheck();
        }



        /**
         * Routes 
         **/
        if (file_exists($endpoints = Base::path('app/Resources/endpoints.php'))) {

            $this->endpoints = require $endpoints;
            
        } else {
            throw new \Exception(Base::lang('error.endpoint_file_is_not_found'));
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
     * Exclude while in maintenance
     * @param array routes            routes to exclude while in maintenance.
     * @return this
     **/
    public function excludeWhileInMaintenance($routes = []) {

        $this->excludedRoutes = $routes;

        return $this;

    }


    /**
     * 
     * App starter
     * @return this
     **/
    public function run() {

        // IP Block
        $blockList = file_exists($file = Base::path('app/Storage/security/ip_blacklist.json')) ? json_decode(file_get_contents($file), true) : [];
        if (isset($blockList[Base::getIp()]) !== false) {

            $this->response->statusCode = 403;
            $this->response->title = Base::lang('err');
            $this->response->arguments = [
                'error' => '403',
                'output' => Base::lang('error.ip_blocked')
            ];

            $this->response();

            return $this;

        }

        $notFound = true;
        
        /**
         * exact expression
         **/
        $route = null;
        if (isset($this->routes[$this->request->uri]) !== false) {
            $route = $this->routes[$this->request->uri];
            $this->endpoint = trim($this->request->uri, '/');
        }

        
        /**
         * Parse request 
         **/

        if (is_null($route)) {

            $fromCache = false;
            if (Base::config('settings.route_cache')) {
                $routeHash = md5(trim($this->request->uri, '/'));

                if (file_exists($file = Base::path('app/Storage/route_cache/' . $routeHash . '.json'))) {
                    $routeCache = json_decode(file_get_contents($file), true);
                    $this->request->attributes = $routeCache['attributes'];
                    $this->endpoint = $routeCache['endpoint'];
                    $route = $routeCache['details'];
                    $fromCache = true;
                    $notFound = false;
                }
            }

            if (! $fromCache) {
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
                        if (($totalPath = count($explodedPath)) === count($explodedRequest)) {

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
                            $total = count($explodedPath);
                            foreach ($explodedPath as $pathIndex => $pathBody) {

                                if ($pathBody == $explodedRequest[$pathIndex] || in_array($pathBody, $expMatches) !== false) { // direct directory check

                                    if (in_array($pathBody, $expMatches) !== false) {
                                        // extract as attribute
                                        $this->request->attributes[ltrim($pathBody, ':')] = Base::filter($explodedRequest[$pathIndex]);
                                    }

                                    if ($totalPath === ($pathIndex + 1)) {
                                        $route = $details;
                                        $routePath = $path;
                                        $notFound = false;
                                    }
                                    
                                } else {
                                    break;
                                }
                            }

                            if (isset($routePath) !== false) {

                                $this->endpoint = trim($routePath, '/');
                            }
                        }
                    }
                }
                if (Base::config('settings.route_cache')) {

                    if (! is_dir($dir = Base::path('app/Storage'))) mkdir($dir);
                    if (! is_dir($dir = Base::path('app/Storage/route_cache'))) mkdir($dir);

                    $cacheContent['attributes'] = $this->request->attributes;
                    $cacheContent['endpoint'] = $this->endpoint;
                    $cacheContent['details'] = $route;

                    file_put_contents($file, json_encode($cacheContent));
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

            // Maintenance Mode
            if (Base::config('settings.maintenance_mode') AND (! $this->authority('/management') AND ! in_array($this->endpoint, $this->excludedRoutes))) {

                $desc = Base::config('settings.maintenance_mode_desc');
                $this->response->statusCode = 503;
                $this->response->title = Base::lang('err');
                $this->response->arguments = [
                    'error' => '503',
                    'output' => $desc ? json_decode($desc, true) : $desc
                ];

                $this->response();
                return $this;
            }

            if (isset($route[$this->request->method]) !== false) {

                $route = $route[$this->request->method];

                /**
                 * 
                 * Middleware step
                 **/

                $next = true;

                if (isset($route['middlewares']) !== false) {

                    foreach ($route['middlewares'] as $middleware) {

                        // for log
                        $this->action->middleware[] = $middleware;

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

                        $this->action->controller = $route['controller'];

                        $controller = explode('@', $route['controller'], 2);

                        $method = $controller[1];
                        $class = 'KN\\Controllers\\' . $controller[0];

                        $controller = (new $class(
                            $this
                        ))->$method();

                        /**
                         * Controller alerts 
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
                         * Output 
                         **/
                        if (isset($controller['output']) !== false)
                            $this->response->output = $controller['output'];

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

                        if (isset($controller['view']) !== false OR is_null($controller['view']))
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

                $this->response();

            }

        }

        return $this;
    }


    /**
     * Extract created response
     * @return void
     **/
    public function response() {

        Base::http($this->response->statusCode);
        if ($this->response->statusCode === 503) {
            Base::http('retry_after');
        }
        $next = true;

        if ($this->response->statusCode === 200) {

            if (! empty($this->response->redirect)) {

                $second = (is_array($this->response->redirect) ? $this->response->redirect[1] : null);
                if (empty($second)) {
                    $next = false;
                    if (count($this->response->alerts)) {
                        Base::setSession($this->response->alerts, 'alerts');
                    }
                }

                Base::http('refresh', [
                    'url' => (is_array($this->response->redirect) ? $this->response->redirect[0] : $this->response->redirect),
                    'second' => $second
                ]);

            }

            if ($next AND $this->response->view !== '') {

                if (is_string($this->response->view)) {
                    $viewFile = $this->response->view;
                    $viewLayout = 'app';
                } elseif (is_array($this->response->view) AND count($this->response->view) === 2) {
                    $viewFile = $this->response->view[0];
                    $viewLayout = $this->response->view[1];
                } elseif (is_null($this->response->view)) {
                    $viewFile = null;
                    $viewLayout = null;
                } else {
                    throw new \Exception(Base::lang('error.view_definition_not_found'));
                }

                $this->view($viewFile, 
                    $this->response->arguments, 
                    $viewLayout
                );

            }

        } else {


            if (! empty($this->response->redirect)) {

                $second = (is_array($this->response->redirect) ? $this->response->redirect[1] : null);
                if (empty($second)) {
                    $next = false;
                    if (count($this->response->alerts)) {
                        Base::setSession($this->response->alerts, 'alerts');
                    }
                }

                Base::http('refresh', [
                    'url' => (is_array($this->response->redirect) ? $this->response->redirect[0] : $this->response->redirect),
                    'second' => $second
                ]);

            } 

            if ($next) {
                $this->view(
                    $this->response->statusCode, 
                    $this->response->arguments, 
                    'error'
                );
            }
        }
        
        if ($this->log AND 
            (Base::config('settings.log') OR 
                (!Base::config('settings.log') AND $this->response->statusCode !== 200)
            )
        ) {

            $this->action->middleware = implode(',', $this->action->middleware);

            foreach ($this->action as $key => $val) 
                if ($val === '') $this->action->{$key} = null;

            (new Log())->add([
                'request'       => $this->request,
                'response'      => $this->response,
                'action'        => $this->action
            ]);
        }
    }


    /**
     *
     * View Page 
     * @param string|null  file          view file name
     * @param array   arguments     needed view variables 
     * @param string  layout        page structure indicator
     * @return this
     **/

    public function view($file = null, $arguments = [], $layout = 'app') {


        /**
         * 
         * Send HTTP status code.
         **/

        Base::http($this->response->statusCode);

        if (is_null($file)) {

            /**
             * for API or Fetch/XHR output 
             **/

            if (isset($arguments['alerts']) === false AND count($this->response->alerts)) {
                $arguments['alerts'] = Base::sessionStoredAlert($this->response->alerts);
            }
            Base::http('content_type', ['content' => 'json', 'write' => json_encode($arguments)]);

        } else {

            /**
             * 
             * Arguments are extracted and the title is defined.
             **/
            $view = true;
            // View Cache Get
            if (Base::config('settings.view_cache')) {

                $cacheHash = md5($file.json_encode($arguments).$layout);
                if (file_exists($cacheFile = Base::path('app/Storage/view_cache/' . $cacheHash . '.html')) AND 
                    strtotime(date('Y-m-d H:i:s +10 minutes', filemtime($cacheFile))) < time()
                ) {
                    $view = false;
                    echo file_get_contents($cacheFile);
                }
            }

            if ($view) {

                $arguments['title'] = isset($arguments['title']) !== false ? 
                    str_replace(
                        ['[TITLE]', '[APP]'], 
                        [$arguments['title'], 
                        Base::config('settings.name')], Base::config('app.title_format')) 
                        : Base::config('settings.name');

                extract($arguments);

                if (isset($description) === false) {
                    $description = @json_decode(Base::config('settings.description'), true);
                    if (! $description) $description = Base::config('settings.description');
                    else $description = $description[Base::lang('lang.code')];
                }


                /**
                 * 
                 * Prepare the page structure according to the format.
                 **/

                $layoutVars = Base::path('app/Resources/view/_layouts/_' . $layout . '.php');
                $layout = file_exists($layoutVars) ? (require $layoutVars) : ['_'];

                foreach ($layout as $part) {

                    if ($part === '_')
                        $part = strpos($file, '.') !== false ? str_replace('.', '/', $file) : $file;
                    else
                        $part = '_parts/' . $part;

                    if (file_exists($req = Base::path('app/Resources/view/' . $part . '.php'))) {
                        require $req;
                    }

                }

                // View Cache Set
                if (Base::config('settings.view_cache')) {

                    if (! is_dir($dir = Base::path('app/Storage'))) mkdir($dir);
                    if (! is_dir($dir = Base::path('app/Storage/view_cache'))) mkdir($dir);

                    file_put_contents($cacheFile, ob_get_contents());

                }
            }

        }

    }


    /**
     * 
     *  Authority check with session
     *  @return this 
     **/
    public function authCheck() {

        $authCheck = (new Auth([
            'response' => $this->response,
            'request' => $this->request
        ]))->check();

        $this->auth = $authCheck['auth'];
        if ($authCheck['redirect'])
            $this->response->redirect = $authCheck['redirect'];

        if ($authCheck['alerts'])
            $this->response->alerts[] = $authCheck['alerts'];

        return $this;
    }


    /**
     *  URL Generator
     *  @param string $route
     *  @return string $url
     **/
    public function url($route) {

        return Base::base($route);

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
            (! $exact AND strpos($this->request->uri, $link) !== false)
        ) {
            $return = ' ' . trim($class);
        }
        return $return;
    }


    /**
     * Authority check for a endpoint
     * @param string $endpoint  
     * @return bool
     */
    public function authority($endpoint) {

        $endpoint = trim($endpoint, '/');
        $routes = Base::userData('routes');

        if (! is_object($routes)) $routes = [];
        else $routes = (array)$routes;

        if (in_array($endpoint, $routes) !== false) {
            return true;
        } else {
            return false;
        }
    }
}