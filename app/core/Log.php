<?php

/**
 * @package KN
 * @subpackage KN Log
 */

declare(strict_types=1);

namespace App\Core;

use App\Core\DB;
use App\Helpers\KN;

class Log {

    public function __construct () {

       
    }

    public function add($args) {

        $args = KN::privateDataCleaner($args);

        $add = new DB();
        $add->table('logs')
            ->insert([
                'date'          => time(),
                'endpoint'      => $args['request']['request'],
                'http_status'   => $args['http_status'],
                'auth_code'     => isset($_COOKIE[KN_SESSION_NAME]) !== false ? $_COOKIE[KN_SESSION_NAME] : null,
                'user_id'       => isset($_SESSION['user']->id) !== false ? $_SESSION['user']->id : null,
                'ip'            => KN::getIp(),
                'header'        => KN::getHeader(),
                'request'       => json_encode($args['request']),
                'response'      => is_array($args['response']) ? json_encode($args['response']) : $args['response'],

            ]);

    }

}