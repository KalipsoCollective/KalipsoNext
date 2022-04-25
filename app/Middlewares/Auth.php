<?php

/**
 * @package KN
 * @subpackage Auth Middleware
 */

declare(strict_types=1);

namespace KN\Middlewares;

use KN\Helpers\Base;
use KN\Core\Middleware;
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
                'statusCode' => 401,
                'next'   => false,
                'arguments' => [
                    'title' => Base::lang('err'),
                    'error' => '401',
                    'output' => Base::lang('error.unauthorized')
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
                'statusCode' => 302,
                'redirect' => '/'
            ];
        }

    }
}