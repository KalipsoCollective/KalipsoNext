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
    public $routes = [];
    public $lang = 'en';

    /**
     *  
     * Request handler 
     **/

    public function __construct() 
    {

        global $languageFile;

        // X_POWERED_BY header - please don't remove! 
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
         *  Handle request
         **/
        $this->request = (object)[];

        $url = parse_url($_SERVER['REQUEST_URI']);
        $this->request->uri = '/' . trim($url['path'] === '/' ? $url['path'] : rtrim($url['path'], '/'), '/');
        $this->request->method = strtoupper(empty($_SERVER['REQUEST_METHOD']) ? 'GET' : $_SERVER['REQUEST_METHOD']);


        // Clean GET Parameters
        if (isset($_GET) !== false AND count($_GET)) {
            foreach ($_GET as $key => $value) {
                $this->request->params[$key] = Base::filter($value);
            }
        }

        // Clean POST Parameters
        if (isset($_POST) !== false AND count($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->request->params[$key] = Base::filter($value);
            }
        }

    }


    /**
     * 
     *  Router register 
     **/

    public function route($method, $route, $controller, $middlewares = [])
    {
        $methods = strpos($method, ',') ? explode(',', $method) : [$method];

        foreach ($methods as $method)
            $this->routes[$route][$method] = [$controller, $middlewares];

    }


    /**
     *
     * Multi route register 
     **/
    public function routes($routes) {

        foreach ($routes as $route)
            $this->route(...$route);

        return $this;
    }


    /**
     * 
     * App starter
     **/

    public function run() {

        /**
         *
         * Method step 
         **/
        $route = isset($this->routes[$this->request->uri]) !== false ?
            $this->routes[$this->request->uri] : [];

        if (! count($route)) {
            $this->request->status = 404;
            $this->view('error', [
                'title' => '',
                'error' => '404',
                'output' => ''
            ], 'error');
        }


        Base::dump($route);

    }


    /**
     *
     * View Page 
     **/
    public function view($layout = null, $file, $arguments = []) {


    }
}