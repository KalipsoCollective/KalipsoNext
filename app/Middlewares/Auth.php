<?php

/**
 * @package KN
 * @subpackage Auth Middleware
 */

declare(strict_types=1);

namespace KN\Middlewares;

use KN\Helpers\Base;
use KN\Core\Middleware;
use KN\Core\Auth as CoreAuth;
use KN\Model\Users;

final class Auth extends Middleware {

    public function with() {

        $authenticated = false;
        if ($this->get()->authority($this->get('endpoint'))) {
            $authenticated = true;
        }

        if ($this->get('auth') AND $authenticated) {
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

    public function verifyAccount() {

        if (isset($this->get('request')->params['verify-account']) !== false) {

            $token = $this->get('request')->params['verify-account'];

            $userModel = (new Users());
            $getUser = $userModel->where('status', 'passive')->where('token', $token)->get();

            if(! empty($getUser)) {

                $update = $userModel->where('id', $getUser->id)->update([
                    'token' => Base::tokenGenerator(80),
                    'status' => 'active'
                ]);

                if ($update) {

                    return [
                        'status' => false,
                        'next'   => false,
                        'statusCode' => 200,
                        'redirect' => '/',
                        'alerts' => [
                            [
                                'status' => 'success',
                                'message' => Base::lang('base.verify_email_success')
                            ]
                        ]
                    ];

                } else {

                    return [
                        'status' => false,
                        'next'   => false,
                        'statusCode' => 200,
                        'redirect' => '/',
                        'alerts' => [
                            [
                                'status' => 'warning',
                                'message' => Base::lang('base.verify_email_problem')
                            ]
                        ]
                    ];
                }

            } else {

                return [
                    'status' => false,
                    'next'   => false,
                    'statusCode' => 404,
                    'redirect' => '/',
                    'alerts' => [
                        [
                            'status' => 'error',
                            'message' => Base::lang('base.verify_email_not_found')
                        ]
                    ]
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