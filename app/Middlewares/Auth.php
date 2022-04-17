<?php

/**
 * @package KN
 * @subpackage Auth Middleware
 */

declare(strict_types=1);

namespace KN\Middlewares;

use KN\Helpers\Base;
use KN\Middlewares\Middleware;
use KN\Model\User as UserModel;

final class Auth extends Middleware {

    public function with() {

        if ($this->get('auth')) {
            return [
                'status' => true,
                'next'   => true
            ];
        } else {
            return [
                'status' => false,
                'next'   => false,
                'view'   => [
                    'error', 
                    [
                        'title' => Base::lang('err'),
                        'error' => '401',
                        'output' => Base::lang('error.unauthorized')
                    ], 
                    'error'
                ]
            ];
        }

    }

    public function withOut() {

        if (! $this->get('auth')) {
            return [
                'status' => true,
                'next'   => true
            ];
        } else {
            return [
                'status' => false,
                'next'   => false,
                'redirect' => ['/' , 0, 302]
            ];
        }

    }
}