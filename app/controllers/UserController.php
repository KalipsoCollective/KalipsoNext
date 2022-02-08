<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\KN;
use App\Model\User;

final class UserController {

    public $request = [];
    public $response = [];
    protected $model = [];

    public function __construct($request = []) {

        $this->request = $request;

    }

    public function index() {

        echo 'Welcome!';

    }

    public function login() {


        if ($this->request['request_method'] == 'POST') {

            extract(KN::input([
                'username'  => 'nulled_text',
                'password'  => 'nulled_text'
            ], $this->request['parameters']));

            if (! is_null($username) AND ! is_null($password)) {

                $this->model = (new User());

                $get = $this->model->getUser('email_or_username', $username);

                if ($get) {

                    if ($get->status == 'deleted') {

                        $this->response['messages'][] = [
                            'status' => 'error',
                            'title'  => KN::lang('error'),
                            'message'=> KN::lang('your_account_has_been_blocked')
                        ];

                    } else {

                        if (password_verify($password, $get->password)) {

                            $logged = KN::setSession($get);

                            $get->view_points = (object) explode(',', $get->view_points);
                            $get->action_points = (object) explode(',', $get->action_points);

                            if ($logged) {
                                $logged = $this->model->saveSession($get, $this->request['request']);
                            }
                            

                            if ($logged) {

                                $this->response['redirect'] = [4, KN::base()];
                                $this->response['messages'][] = [
                                    'status' => 'success',
                                    'title'  => KN::lang('success'),
                                    'message'=> KN::lang('logging_in'),
                                ];

                            } else {

                                $this->response['messages'][] = [
                                    'status' => 'alert',
                                    'title'  => KN::lang('warning'),
                                    'message'=> KN::lang('start_session_problem')
                                ];

                            }

                        } else {

                            $this->response['messages'][] = [
                                'status' => 'alert',
                                'title'  => KN::lang('warning'),
                                'message'=> KN::lang('your_login_info_incorrect')
                            ];

                        }

                    }

                } else {

                    $this->response['messages'][] = [
                        'status' => 'alert',
                        'title'  => KN::lang('warning'),
                        'message'=> KN::lang('account_not_found')
                    ];

                }

            } else {

                $this->response['messages'][] = [
                    'status' => 'error',
                    'title'  => KN::lang('alert'),
                    'message'=> KN::lang('form_cannot_empty')
                ];

            }
            
        }

        return KN::layout('user/login', [
            'title'     => KN::lang('login') . ' | ' . KN::config('app.name'),
            'request'   => $this->request,
            'response'  => $this->response
        ]);

    }


    public function register() {

        KN::layout('user/register');

    }


    public function recovery() {

        KN::layout('user/recovery');

    }


    public function account() {

        if (isset($this->request['parameters']['logout']) !== false) {
            $this->response['redirect'] = [4, KN::base()];
            $this->response['messages'][] = [
                'status' => 'success',
                'title'  => KN::lang('success'),
                'message'=> KN::lang('logging_out'),
            ];

        }

        KN::layout('user/account', [
            'title'     => KN::lang('account') . ' | ' . KN::config('app.name'),
            'request'   => $this->request,
            'response'  => $this->response
        ]);

        if (isset($this->request['parameters']['logout']) !== false) {
            $this->logout();
        }

    }

    public function logout($authCode = null) {

        $this->model = (new User());
        $this->model->clearSession($authCode);
        KN::clearSession();

    }

    public function checkSession() {

        $return = null;
        if (isset($_COOKIE[KN_SESSION_NAME]) !== false) {

            $authCode = $_COOKIE[KN_SESSION_NAME];
            $this->model = (new User());
            $get = $this->model->getSession($authCode, $this->request['request']);

            if (is_null($get) OR ! $get) {
                $this->logout($authCode);
            } elseif (is_object($get)) {
                KN::setSession($get);
                $return = true;
            } else {
                $return = true;
            }

        }
        return $return;

    }

}