<?php

/**
 * @package KN
 * @subpackage CSRF Middleware
 */

declare(strict_types=1);

namespace KN\Middlewares;

use KN\Helpers\Base;
use KN\Middlewares\Middleware;

final class CSRF extends Middleware {

    public function validate() {

        if ($this->get('request')->method === 'POST') {

            if (isset($this->get('request')->params['_token']) === false) {

                return [
                    'status' => false,
                    'statusCode' => 401,
                    'next'   => false,
                    'arguments' => [
                        'title' => Base::lang('err'),
                        'error' => '401',
                        'output' => Base::lang('error.csrf_token_mismatch')
                    ]
                ];

            } elseif (! Base::verifyCSRF($this->request['parameters']['_token'])) {

                return [
                    'status' => false,
                    'statusCode' => 401,
                    'next'   => false,
                    'arguments' => [
                        'title' => Base::lang('err'),
                        'error' => '401',
                        'output' => Base::lang('error.csrf_token_incorrect')
                    ]
                ];

            } else {

                return [
                    'status' => true,
                    'next'   => true
                ];
            }
        } else {

            return [
                'status' => true,
                'next'   => true
            ];
        }

    }
}