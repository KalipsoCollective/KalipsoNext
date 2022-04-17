<?php

/**
 * @package KN
 * @subpackage Middleware
 */

declare(strict_types=1);

namespace KN\Middlewares;

use KN\Helpers\Base;

class Middleware {


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