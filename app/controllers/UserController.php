<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\KN;
use App\Model\User;
use App\Core\Notification;

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

        if ($this->request['request_method'] == 'POST') {

            extract(KN::input([
                'username'  => 'nulled_text',
                'email'     => 'nulled_email',
                'name'      => 'nulled_text',
                'surname'   => 'nulled_text',
                'password'  => 'nulled_password',
            ], $this->request['parameters']));

            if (! is_null($username) AND ! is_null($email) AND ! is_null($password)) {

                $this->model = (new User());

                $getWithEmail = $this->model->getUser('email', $email);
                if ( !$getWithEmail) {

                    $getWithUsername = $this->model->getUser('u_name', $username);
                    if ( !$getWithUsername) {

                        $row = [
                            'u_name'    => $username,
                            'f_name'    => $name,
                            'l_name'    => $surname,
                            'email'     => $email,
                            'password'  => $password,
                            'token'     => KN::tokenGenerator(80),
                            'role_id'   => KN::config('settings.default_user_role'),
                            'created_at'=> time(),
                            'status'    => 'passive'
                        ];

                        $insert = $this->model->addUser($row);

                        if ($insert) {

                            $row['id'] = $insert;
                            (new Notification)->add('registration', $row);

                            $this->response['redirect'] = [4, KN::base('account/login')];
                            $this->response['messages'][] = [
                                'status' => 'success',
                                'title'  => KN::lang('success'),
                                'message'=> KN::lang('registration_successful'),
                            ];

                        } else {

                            $this->response['messages'][] = [
                                'status' => 'alert',
                                'title'  => KN::lang('warning'),
                                'message'=> KN::lang('registration_problem')
                            ];

                        }

                    } else {

                        $this->response['messages'][] = [
                            'status' => 'alert',
                            'title'  => KN::lang('warning'),
                            'message'=> KN::lang('username_is_already_used')
                        ];

                    }

                } else {

                    $this->response['messages'][] = [
                        'status' => 'alert',
                        'title'  => KN::lang('warning'),
                        'message'=> KN::lang('email_is_already_used')
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

        return KN::layout('user/register', [
            'title'     => KN::lang('register') . ' | ' . KN::config('app.name'),
            'request'   => $this->request,
            'response'  => $this->response
        ]);

    }


    public function recovery() {

        if ($this->request['request_method'] == 'POST') {

            extract(KN::input([
                'email'     => 'nulled_email',
                'token'     => 'nulled_text',
                'password'  => 'nulled_text',
            ], $this->request['parameters']));

            if (! is_null($email)) {

                $this->model = (new User());

                $getWithEmail = $this->model->getUser('email', $email);

                if ( $getWithEmail ) {
                    $getWithEmail = (array) $getWithEmail;

                    if ((new Notification)->add('recovery_request', $getWithEmail)) {

                        unset($this->request['parameters']['email']);

                        $this->response['redirect'] = [4, KN::base('account/login')];
                        $this->response['messages'][] = [
                            'status' => 'success',
                            'title'  => KN::lang('success'),
                            'message'=> KN::lang('recovery_request_successful'),
                        ];

                    } else {

                        $this->response['messages'][] = [
                            'status' => 'alert',
                            'title'  => KN::lang('warning'),
                            'message'=> KN::lang('recovery_request_problem')
                        ];

                    }

                } else {

                    $this->response['messages'][] = [
                        'status' => 'alert',
                        'title'  => KN::lang('warning'),
                        'message'=> KN::lang('account_not_found')
                    ];

                }

            } elseif (! is_null($token) AND ! is_null($password)) {

                $this->model = (new User());

                $getWithToken = $this->model->getUser('token', $token);

                if ( $getWithToken ) {
                    $getWithToken = (array) $getWithToken;
                    $update = $this->model
                        ->updateUser([
                            'token'         => KN::tokenGenerator(80),
                            'password'      => password_hash($password, PASSWORD_DEFAULT),
                            'updated_at'    => time()
                        ], $getWithToken['id']
                    );

                    if ($update) {

                        if ((new Notification)->add('recovery_account', $getWithToken)) {

                            $this->model->removeSessions($getWithToken['id']);
                            unset($this->request['parameters']['email']);

                            $this->response['redirect'] = [4, KN::base('account/login')];
                            $this->response['messages'][] = [
                                'status' => 'success',
                                'title'  => KN::lang('success'),
                                'message'=> KN::lang('recovery_account_successful'),
                            ];

                        } else {

                            $this->response['messages'][] = [
                                'status' => 'alert',
                                'title'  => KN::lang('warning'),
                                'message'=> KN::lang('recovery_account_problem')
                            ];

                        }

                    } else {
                        $this->response['messages'][] = [
                            'status' => 'alert',
                            'title'  => KN::lang('warning'),
                            'message'=> KN::lang('recovery_account_problem')
                        ];
                    }

                } else {

                    if (isset($this->request['parameters']['token']) !== false) 
                        unset($this->request['parameters']['token']);

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

        return KN::layout('user/recovery', [
            'title'     => KN::lang('recovery_account') . ' | ' . KN::config('app.name'),
            'request'   => $this->request,
            'response'  => $this->response
        ]);

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

        switch ($this->request['request']) {
            case '/account/profile':
                $title = KN::lang('account') . ' · ' . KN::lang('profile');
                break;

            case '/account/sessions':
                $title = KN::lang('account') . ' · ' . KN::lang('sessions');
                // $this->response['messages']
                break;

            default:
                $title = KN::lang('account');
                break;
        }

        KN::layout('user/account', [
            'title'     => $title,
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


    public function verifyAccount($token) {

        $this->model = (new User());
        return $this->model->verifyAccount($token);

    }

}