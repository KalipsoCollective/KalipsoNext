<?php

/**
 * @package KN
 * @subpackage KN Log
 */

declare(strict_types=1);

namespace KN\Core;

use KN\Core\DB;
use KN\Helpers\Base;

class Log {

    public function __construct () {

       
    }

    public function add($args) {

        $args = Base::privateDataCleaner($args);

        $add = new DB();
        $add->table('logs')
            ->insert([
                'date'          => time(),
                'endpoint'      => $args['request']['request'],
                'http_status'   => $args['http_status'],
                'auth_code'     => isset($_COOKIE[KN_SESSION_NAME]) !== false ? $_COOKIE[KN_SESSION_NAME] : null,
                'user_id'       => isset($_SESSION['user']->id) !== false ? $_SESSION['user']->id : null,
                'ip'            => Base::getIp(),
                'header'        => Base::getHeader(),
                'request'       => json_encode($args['request']),
                'response'      => is_array($args['response']) ? json_encode($args['response']) : $args['response'],

            ]);

    }

}