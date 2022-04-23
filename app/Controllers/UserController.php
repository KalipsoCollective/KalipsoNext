<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Controllers\Controller;
use KN\Helpers\Base;
use KN\Model\Users;

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

    public function register() {

        $alerts = [];

        if ($this->get('request')->method === 'POST') {

            extract(Base::input([
                'email' => 'nulled_email', 
                'username' => 'nulled_text',
                'name' => 'nulled_text',
                'surname' => 'nulled_text',
                'password' => 'nulled_text'
            ], $this->get('request')->params));

            if (! is_null($username) AND ! is_null($email) AND ! is_null($password)) {

                $users = (new Users());

                $getWithEmail = $users->select('email')->where('email', $email)->get();
                if ( !$getWithEmail) {

                    $users = (new Users());
                    $getWithUsername = $users->select('u_name')->where('u_name', $username)->get();
                    if ( !$getWithUsername) {

                        $row = [
                            'u_name'    => $username,
                            'f_name'    => $name,
                            'l_name'    => $surname,
                            'email'     => $email,
                            'password'  => $password,
                            'token'     => Base::tokenGenerator(80),
                            'role_id'   => Base::config('settings.default_user_role'),
                            'created_at'=> time(),
                            'status'    => 'passive'
                        ];

                        $insert = $users->addUser($row);

                        if ($insert) {

                            $row['id'] = $insert;
                            (new Notification)->add('registration', $row);

                            $alerts[] = [
                                'status' => 'success',
                                'message' => Base::lang('error.registration_successful')
                            ];
                            $redirect = [$this->get('url')('/auth/login'), 4];

                        } else {

                            $alerts[] = [
                                'status' => 'warning',
                                'message' => Base::lang('error.registration_problem')
                            ];

                        }

                    } else {

                        $alerts[] = [
                            'status' => 'warning',
                            'message' => Base::lang('error.username_is_already_used')
                        ];

                    }

                } else {

                    $alerts[] = [
                        'status' => 'warning',
                        'message' => Base::lang('error.email_is_already_used')
                    ];

                }

            } else {

                $alerts[] = [
                    'status' => 'warning',
                    'message' => Base::lang('error.form_cannot_empty')
                ];

            }
            
        }

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.register'),
                'output' => Base::lang('base.register_message')
            ],
            'alerts' => $alerts,
            'view' => 'user.register',
        ];

    }

}