<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Helpers\Base;
use KN\Model\User;
use KN\Core\Notification;

final class UserController {

    public $request = [];
    public $response = [];
    protected $model = [];

    public function __construct($request = null) {

        $this->request = $request;

    }

    public function index() {

        echo 'Welcome!';

    }

    public function login() {


        if ($this->request['request_method'] == 'POST') {

            extract(Base::input([
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
                            'title'  => Base::lang('error'),
                            'message'=> Base::lang('your_account_has_been_blocked')
                        ];

                    } else {

                        if (password_verify($password, $get->password)) {

                            $logged = Base::setSession($get);

                            $get->view_points = (object) explode(',', $get->view_points);
                            $get->action_points = (object) explode(',', $get->action_points);

                            if ($logged) {
                                $logged = $this->model->saveSession($get, $this->request['request']);
                            }
                            

                            if ($logged) {

                                $this->response['redirect'] = [4, Base::base()];
                                $this->response['messages'][] = [
                                    'status' => 'success',
                                    'title'  => Base::lang('success'),
                                    'message'=> Base::lang('logging_in'),
                                ];

                            } else {

                                $this->response['messages'][] = [
                                    'status' => 'alert',
                                    'title'  => Base::lang('warning'),
                                    'message'=> Base::lang('start_session_problem')
                                ];

                            }

                        } else {

                            $this->response['messages'][] = [
                                'status' => 'alert',
                                'title'  => Base::lang('warning'),
                                'message'=> Base::lang('your_login_info_incorrect')
                            ];

                        }

                    }

                } else {

                    $this->response['messages'][] = [
                        'status' => 'alert',
                        'title'  => Base::lang('warning'),
                        'message'=> Base::lang('account_not_found')
                    ];

                }

            } else {

                $this->response['messages'][] = [
                    'status' => 'error',
                    'title'  => Base::lang('alert'),
                    'message'=> Base::lang('form_cannot_empty')
                ];

            }
            
        }

        return Base::layout('user/login', [
            'title'     => Base::lang('login') . ' | ' . Base::config('app.name'),
            'request'   => $this->request,
            'response'  => $this->response
        ]);

    }


    public function register() {

        if ($this->request['request_method'] == 'POST') {

            extract(Base::input([
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
                            'token'     => Base::tokenGenerator(80),
                            'role_id'   => Base::config('settings.default_user_role'),
                            'created_at'=> time(),
                            'status'    => 'passive'
                        ];

                        $insert = $this->model->addUser($row);

                        if ($insert) {

                            $row['id'] = $insert;
                            (new Notification)->add('registration', $row);

                            $this->response['redirect'] = [4, Base::base('account/login')];
                            $this->response['messages'][] = [
                                'status' => 'success',
                                'title'  => Base::lang('success'),
                                'message'=> Base::lang('registration_successful'),
                            ];

                        } else {

                            $this->response['messages'][] = [
                                'status' => 'alert',
                                'title'  => Base::lang('warning'),
                                'message'=> Base::lang('registration_problem')
                            ];

                        }

                    } else {

                        $this->response['messages'][] = [
                            'status' => 'alert',
                            'title'  => Base::lang('warning'),
                            'message'=> Base::lang('username_is_already_used')
                        ];

                    }

                } else {

                    $this->response['messages'][] = [
                        'status' => 'alert',
                        'title'  => Base::lang('warning'),
                        'message'=> Base::lang('email_is_already_used')
                    ];

                }

            } else {

                $this->response['messages'][] = [
                    'status' => 'error',
                    'title'  => Base::lang('alert'),
                    'message'=> Base::lang('form_cannot_empty')
                ];

            }
            
        }

        return Base::layout('user/register', [
            'title'     => Base::lang('register') . ' | ' . Base::config('app.name'),
            'request'   => $this->request,
            'response'  => $this->response
        ]);

    }


    public function recovery() {

        if ($this->request['request_method'] == 'POST') {

            extract(Base::input([
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

                        $this->response['redirect'] = [4, Base::base('account/login')];
                        $this->response['messages'][] = [
                            'status' => 'success',
                            'title'  => Base::lang('success'),
                            'message'=> Base::lang('recovery_request_successful'),
                        ];

                    } else {

                        $this->response['messages'][] = [
                            'status' => 'alert',
                            'title'  => Base::lang('warning'),
                            'message'=> Base::lang('recovery_request_problem')
                        ];

                    }

                } else {

                    $this->response['messages'][] = [
                        'status' => 'alert',
                        'title'  => Base::lang('warning'),
                        'message'=> Base::lang('account_not_found')
                    ];

                }

            } elseif (! is_null($token) AND ! is_null($password)) {

                $this->model = (new User());

                $getWithToken = $this->model->getUser('token', $token);

                if ( $getWithToken ) {
                    $getWithToken = (array) $getWithToken;
                    $update = $this->model
                        ->updateUser([
                            'token'         => Base::tokenGenerator(80),
                            'password'      => password_hash($password, PASSWORD_DEFAULT),
                            'updated_at'    => time()
                        ], $getWithToken['id']
                    );

                    if ($update) {

                        if ((new Notification)->add('recovery_account', $getWithToken)) {

                            $this->model->removeSessions($getWithToken['id']);
                            unset($this->request['parameters']['email']);

                            $this->response['redirect'] = [4, Base::base('account/login')];
                            $this->response['messages'][] = [
                                'status' => 'success',
                                'title'  => Base::lang('success'),
                                'message'=> Base::lang('recovery_account_successful'),
                            ];

                        } else {

                            $this->response['messages'][] = [
                                'status' => 'alert',
                                'title'  => Base::lang('warning'),
                                'message'=> Base::lang('recovery_account_problem')
                            ];

                        }

                    } else {
                        $this->response['messages'][] = [
                            'status' => 'alert',
                            'title'  => Base::lang('warning'),
                            'message'=> Base::lang('recovery_account_problem')
                        ];
                    }

                } else {

                    if (isset($this->request['parameters']['token']) !== false) 
                        unset($this->request['parameters']['token']);

                    $this->response['messages'][] = [
                        'status' => 'alert',
                        'title'  => Base::lang('warning'),
                        'message'=> Base::lang('account_not_found')
                    ];

                }

            } else {

                $this->response['messages'][] = [
                    'status' => 'error',
                    'title'  => Base::lang('alert'),
                    'message'=> Base::lang('form_cannot_empty')
                ];

            }
            
        }

        return Base::layout('user/recovery', [
            'title'     => Base::lang('recovery_account') . ' | ' . Base::config('app.name'),
            'request'   => $this->request,
            'response'  => $this->response
        ]);

    }


    public function account() {

        $arguments = null;

        $this->model = (new User());

        // profile update
        if ($this->request['request_method'] == 'POST') {

            extract(Base::input([
                'f_name'    => 'nulled_text',
                'l_name'    => 'nulled_text',
                'u_name'    => 'nulled_text',
                'email'     => 'nulled_email',
                'b_date'    => 'date',
                'password'  => 'nulled_text',
            ], $this->request['parameters']));

            if (! is_null($f_name) AND ! is_null($l_name) AND ! is_null($u_name) AND ! is_null($email) AND ! is_null($b_date)) {

                $get = $this->model->getUser('id', Base::userData('id'));
                $sessionDestroy = false;
                $statusChange = false;

                if ($get) {

                    if ($get->status == 'passive') {

                        $this->response['messages'][] = [
                            'status' => 'warning',
                            'title'  => Base::lang('warning'),
                            'message'=> Base::lang('your_account_is_not_verified')
                        ];

                    } else {

                        $currentUser = ['id', Base::userData('id')];

                        $update = [
                            'f_name'    => $f_name,
                            'l_name'    => $l_name,
                            'b_date'    => $b_date
                        ];

                        // Username Change
                        $check = false;
                        if ($u_name !== Base::userData('u_name')) {

                            $check = $this->model->getUser('u_name', $u_name, $currentUser);

                            if ($check) {

                                $check = true;

                                $this->response['messages'][] = [
                                    'status' => 'warning',
                                    'title'  => Base::lang('warning'),
                                    'message'=> Base::lang('username_is_already_used')
                                ];

                            } else {

                                $update['u_name'] = $u_name;

                            }

                        }

                        // Email Change
                        if (! $check AND $email !== Base::userData('email')) {

                            $check = $this->model->getUser('email', $email, $currentUser);
                            if ($check) {

                                $check = true;

                                $this->response['messages'][] = [
                                    'status' => 'warning',
                                    'title'  => Base::lang('warning'),
                                    'message'=> Base::lang('email_is_already_used')
                                ];

                            } else {

                                $update['email'] = $email;
                                $update['token'] = Base::tokenGenerator(80);
                                $update['status'] = 'passive';
                                $statusChange = true;

                            }

                        }

                        // Password Change
                        if (! $check AND ! is_null($password)) {

                            $update['password'] = password_hash($password, PASSWORD_DEFAULT);
                            $sessionDestroy = true;
                        }

                        if (! $check) {

                            $save = $this->model->updateUser($update, $currentUser[1]);
                            if ($save) {

                                $get = $this->model->getUser('id', $currentUser[1]);

                                $this->response['messages'][] = [
                                    'status' => 'success',
                                    'title'  => Base::lang('success'),
                                    'message'=> Base::lang('profile_updated'),
                                ];
                                $this->response['redirect'] = [5, Base::base('account/profile')];

                                if ($sessionDestroy) {

                                    $this->logout();
                                    $this->model->removeSessions($currentUser[1]);

                                } else {

                                    $logged = Base::setSession($get);
                                    $get->view_points = (object) explode(',', $get->view_points);
                                    $get->action_points = (object) explode(',', $get->action_points);
                                    if ($logged) {
                                        $logged = $this->model->saveSession($get, $this->request['request']);
                                    }

                                }

                                if ($statusChange) {

                                    (new Notification)->add('email_change', (array) $get);
                                }

                            } else {

                                $this->response['messages'][] = [
                                    'status' => 'alert',
                                    'title'  => Base::lang('warning'),
                                    'message'=> Base::lang('profile_update_problem')
                                ];
                            }


                        }

                    }

                } else {

                    $this->response['messages'][] = [
                        'status' => 'alert',
                        'title'  => Base::lang('warning'),
                        'message'=> Base::lang('form_cannot_empty')
                    ];

                }

            } else {

                $this->response['messages'][] = [
                    'status' => 'alert',
                    'title'  => Base::lang('warning'),
                    'message'=> Base::lang('form_cannot_empty')
                ];

            }
            
        }

        // logout
        if (isset($this->request['parameters']['logout']) !== false) {
            $this->response['redirect'] = [4, Base::base()];
            $this->response['messages'][] = [
                'status' => 'success',
                'title'  => Base::lang('success'),
                'message'=> Base::lang('logging_out'),
            ];

        }

        switch ($this->request['request']) {
            case '/account/profile':
                $title = Base::lang('account') . ' · ' . Base::lang('profile');
                break;

            case '/account/sessions':
                $title = Base::lang('account') . ' · ' . Base::lang('sessions');
                $arguments['sessions'] = $this->model->getSessions(Base::userData('id'));
                break;

            default:
                $title = Base::lang('account');
                break;
        }

        $push = [
            'title'     => $title,
            'request'   => $this->request,
            'response'  => $this->response
        ];

        if ($arguments) {
            $push['arguments'] = $arguments;
        }

        Base::layout('user/account', $push);

        if (isset($this->request['parameters']['logout']) !== false) {
            $this->logout();
        }

    }

    public function logout($authCode = null) {

        $this->model = (new User());
        $this->model->clearSession($authCode);
        Base::clearSession();

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
                Base::setSession($get);
                $return = true;
            } else {
                $return = true;
            }

        }
        return $return;

    }

}