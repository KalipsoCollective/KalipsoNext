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
                'output' => Base::lang('error.login_message')
            ]
        ];

    }

    public function account() {

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.account'),
                'output' => Base::lang('error.account_message')
            ]
        ];

    }

}