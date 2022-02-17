<?php

/**
 * @package KN
 * @subpackage KN Notification
 */

declare(strict_types=1);

namespace App\Core;

use App\Core\DB;
use App\Helpers\KN;

class Notification {

    public $types;

    public function __construct () {


        // type -> process(email or notification) -> process requirements

        $baseUrl = KN::base();

        $this->types = [
            'registration' => [
                'email' => [
                    'title' => 'noti_email_register_title', 
                    'body' => 'noti_email_register_body', 
                    'args' => [
                        '[USER]' => [
                            'cols' => ['f_name', 'u_name'],
                        ]
                        '[VERIFY_LINK]' => [
                            'cols' => ['token'],
                            'pattern' => ['<a href="' . $baseUrl . '/?verify=[0]">' . KN::lang('verify_email') . '</a>']
                        ]
                    ]
                ],
                'sys'   => ['title' => 'noti_sys_register_title', 'body' => 'noti_sys_register_body'],
            ]
        ];
    }

    public function add($type, $args) {

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