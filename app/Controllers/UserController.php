<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Controllers\Controller;
use KN\Helpers\Base;

final class UserController extends Controller {

    public function login() {

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.login'),
                'output' => Base::lang('base.login_message')
            ],
            'view' => 'user.login',
        ];

    }

    public function account() {

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.account'),
                'output' => Base::lang('base.account_message')
            ],
            'view' => 'user.account',
        ];

    }

}