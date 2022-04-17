<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Helpers\Base;

class Controller {


    /**
     *  @param object container  factory class   
     *  @return void
     **/

    public function __construct($container) {

        $this->container = $container;

    }

    public function get($key) {

        return $this->container->{$key};

    }

}