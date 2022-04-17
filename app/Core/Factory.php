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
     * All request details as object
     **/
    public $request;
    public $response;
    public $routes = [];
    public $lang = 'en';

    /**
     *  
     * Request handler 
     **/

    public function __construct() 
    {

        global $languageFile;

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
         * Language definition 
         **/

        $sessionLanguageParam = Base::getSession('language');
        if (
            ! is_null($sessionLanguageParam) AND 
            file_exists($path = Base::path('app/Resources/localization/'.$sessionLanguageParam.'.php'))
        ) {

            $this->lang = $sessionLanguageParam;
            $languageFile = require $path;

        } elseif (file_exists($path = Base::path('app/Resources/localization/'.$this->lang.'.php'))) {

            $languageFile = require $path;
            Base::setSession($this->lang, 'language');

        } else {

            throw new \Exception("Language file is not found!");

        }


        /**
         * 
         *  Handle request and method
         **/

        $this->response = (object)['status' => 200];
        $this->request = (object)[];

        $url = parse_url($_SERVER['REQUEST_URI']);
        $this->request->uri = '/' . trim($url['path'] === '/' ? $url['path'] : rtrim($url['path'], '/'), '/');
        $this->request->method = strtoupper(empty($_SERVER['REQUEST_METHOD']) ? 'GET' : $_SERVER['REQUEST_METHOD']);


        /**
         * Clean GET parameters
         **/ 

        if (isset($_GET) !== false AND count($_GET)) {
            foreach ($_GET as $key => $value) {
                $this->request->params[$key] = Base::filter($value);
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
                                $this->request->attributes[ltrim($pathBody, ':')] = $explodedRequest[$pathIndex];
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

            $this->response->status = 404;
            $this->view('error', [
                'title' => Base::lang('err'),
                'error' => '404',
                'output' => Base::lang('error.page_not_found')
            ], 'error');

        } else {

            if (isset($route[$this->request->method]) !== false) {

                $route = $route[$this->request->method];
                if (isset($route['controller']) !== false) {

                    $controller = explode('@', $route['controller'], 2);

                    $method = $controller[1];
                    $class = 'KN\\Controllers\\' . $controller[0];

                    $middleware = (new $class(
                        $this
                    ))->$method();


                } else {

                    throw new \Exception(Base::lang('error.controller_not_defined'));
                }
                

            } else { // 405

                $this->response->status = 405;
                $this->view('error', [
                    'title' => Base::lang('err'),
                    'error' => '405',
                    'output' => Base::lang('error.method_not_allowed')
                ], 'error');

            }

        }

        return $this;
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

        Base::http($this->response->status);


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
                $part = $file;
            else
                $part = '_parts/' . $part;

            if (file_exists($req = Base::path('app/Resources/view/' . $part . '.php'))) {
                require $req;
            }

        }

    }
}