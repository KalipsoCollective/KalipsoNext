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
        $this->response = (object)['status' => 200];
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

        $notFound = false;
        /**
         *
         * Method step 
         **/
        $route = isset($this->routes[$this->request->uri]) !== false ?
            $this->routes[$this->request->uri] : [];

        if (! count($route)) {
            $notFound = true;
        }


        if ($notFound) {
            $this->response->status = 404;
            $this->view('error', [
                'title' => Base::lang('err'),
                'error' => '404',
                'output' => Base::lang('error.page_not_found')
            ], 'error');
        }

        return $this;
    }


    /**
     *
     * View Page 
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