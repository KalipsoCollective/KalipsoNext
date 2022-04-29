<?php

/**
 * @package KN
 * @subpackage KN Middleware
 */

declare(strict_types=1);

namespace KN\Core;

use KN\Helpers\Base;

class Middleware {


    /**
     *  @param object container  factory class   
     *  @return void
     **/

    public function __construct($container) {

        $this->container = $container;

    }

    public function get($key = null) {

        return is_null($key) ? $this->container : $this->container->{$key};

    }

}