<?php

/**
 * @package KN
 * @subpackage KN Log
 */

declare(strict_types=1);

namespace KN\Core;

use KN\Model\Log as Model;
use KN\Helpers\Base;

class Log {

    public function __construct () {

       
    }

    public function add($args) {

        $args = Base::privateDataCleaner($args);

        $exec = microtime(true) - KN_START;

        $add = new Model();

        Base::dump([
                'endpoint'      => $args['request']->uri,
                'http_status'   => $args['response']->statusCode,
                'auth_code'     => Base::authCode(),
                'ip'            => Base::getIp(),
                'header'        => Base::getHeader(),
                'request'       => json_encode($args['request']),
                'response'      => json_encode($args['response']),
                'exec_time'     => $exec
            ]);

        /*
        $add = new Model();
        $add->insert([
                'endpoint'      => $args['request']->uri,
                'http_status'   => $args['response']->statusCode,
                'auth_code'     => Base::authCode(),
                'ip'            => Base::getIp(),
                'header'        => Base::getHeader(),
                'request'       => json_encode($args['request']),
                'response'      => json_encode($args['response']),
                'exec_time'     => $exec
            ]); */

    }

}