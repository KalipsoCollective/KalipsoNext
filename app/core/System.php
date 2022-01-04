<?php

/**
 * @package KN
 * @subpackage KN System
 */

declare(strict_types=1);

namespace App\Core;

use App\Helpers\KN;

class System {

    public $route = null;
    public $lang = 'en';

    public function __construct() {

        $this->route = require_once KN::path('app/resources/route.php');

    }

    public function go () {

        $this->route->run();
        
    }

}