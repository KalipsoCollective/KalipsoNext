<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Helpers\Base;

class BaseController {

    public function __construct($container) {

        $this->container = $container;

    }

}