<?php

/**
 * @package KN
 * @subpackage KN Exception Handler
 */

declare(strict_types=1);

namespace App\Core;

use App\Helpers\KN;

class Route {

    public $schema = null;
    public $request = null;
    public $requestMethod = null;
    public $url = null;
    public $params = [];

    public function __construct() {

        $url = parse_url(KN::base(trim(strip_tags($_SERVER['REQUEST_URI']), '/')));
        $this->url = trim($url['path'], '/');
        $this->request = strpos($this->url, '/') ? explode('/', $this->url) : [$this->url];
        $this->request = array_map('urldecode', $this->request);
        $this->requestMethod = strtoupper(empty($_SERVER['REQUEST_METHOD']) ? 'GET' : $_SERVER['REQUEST_METHOD']);

        // GET Parameters
        if (isset($_GET) !== false AND count($_GET)) {
            foreach ($_GET as $key => $value) {
                $this->params[$key] = KN::filter($value);
            }
        }

        // POST Parameters
        if (isset($_POST) !== false AND count($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->params[$key] = KN::filter($value);
            }
        }

    }

    public function addRoutes($schema) {

        $this->schema = $schema;

    }


    public function detect() {

        KN::dump($this->schema);
        KN::dump($this->request);

    }

}