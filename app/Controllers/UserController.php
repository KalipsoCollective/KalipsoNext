<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Core\Controller;
use KN\Helpers\Base;
use KN\Core\Notification;
use KN\Model\Users;
use KN\Model\UserRoles;
use KN\Model\Sessions;

final class UserController extends Controller {

    public function login() {

        $alerts = [];

        if ($this->get('request')->method === 'POST') {

            extract(Base::input([
                'username' => 'nulled_text',
                'password' => 'nulled_text'
            ], $this->get('request')->params));

            if (! is_null($username) AND ! is_null($password)) {

                $users = (new Users());

                $getUser = $users->select('id, u_name, f_name, l_name, email, password, token, role_id, b_date, status')
                    ->where('u_name', $username)->orWhere('email', $username)
                    ->get();

                if ( ! empty($getUser)) {

                    if ($getUser->status == 'deleted') {

                        $alerts[] = [
                            'status' => 'error',
                            'message' => Base::lang('base.your_account_has_been_blocked')
                        ];

                    } else {

                        if (password_verify($password, $getUser->password)) {

                            $userRoles = new UserRoles();
                            $getUserRole = $userRoles->select('routes, name')->where('id', $getUser->role_id)->get();

                            if (! empty($getUserRole)) {

                                $getUser->role_name = $getUserRole->name;
                                $getUser->routes = (object) explode(',', $getUserRole->routes);

                            }
                            $getUser = Base::privateDataCleaner($getUser);

                            $sessions = new Sessions();
                            $logged = $sessions->insert([
                                'auth_code' => Base::authCode(),
                                'user_id' => $getUser->id,
                                'header' => Base::getHeader(),
                                'ip' => Base::getIp(),
                                'role_id' => $getUser->role_id,
                                'last_action_date' => time(),
                                'last_action_point' => $this->get('request')->uri
                            ]);

                            if ($logged) {

                                Base::setSession($getUser, 'user');
                                $alerts[] = [
                                    'status' => 'success',
                                    'message' => Base::lang('base.welcome_back'),
                                ];

                                $redirect = '/auth';

                            } else {

                                $alerts[] = [
                                    'status' => 'warning',
                                    'message' => Base::lang('base.login_problem')
                                ];

                            }

                        } else {

                            $alerts[] = [
                                'status' => 'warning',
                                'message' => Base::lang('base.your_login_info_incorrect')
                            ];

                        }

                    }


                } else {

                    $alerts[] = [
                        'status' => 'warning',
                        'message' => Base::lang('base.account_not_found')
                    ];

                }

            } else {

                $alerts[] = [
                    'status' => 'warning',
                    'message' => Base::lang('base.form_cannot_empty')
                ];

            }
            
        }

        $return = [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.login'),
                'description' => Base::lang('base.login_message')
            ],
            'alerts' => $alerts,
            'view' => 'user.login',
        ];

        if (isset($redirect)) {
            $return['redirect'] = $redirect;
        }

        return $return;

    }

    public function account() {

        $steps = [
            'profile' => [
                'icon' => 'ti ti-tool', 'lang' => 'base.profile'
            ], 
            'sessions' => [
                'icon' => 'ti ti ti-devices', 'lang' => 'base.sessions'
            ],
        ];

        $action = '';

        if (isset($this->get('request')->attributes['action']) !== false)
            $action = $this->get('request')->attributes['action'];

        $title = Base::lang('base.account');
        $output = '';
        $alerts = [];
        $statusCode = 200;

        switch ($action) {
            case 'profile':
                $head = Base::lang('base.profile');
                $title = $head . ' | ' . $title;
                $description = Base::lang('base.profile_message');
                $output = Base::getSession('user');

                if ($this->get('request')->method === 'POST') {

                    extract(Base::input([
                        'email' => 'nulled_email', 
                        'f_name' => 'nulled_text', 
                        'l_name' => 'nulled_text',
                        'b_date' => 'date',
                        'password' => 'nulled_password'
                    ], $this->get('request')->params));

                    if (! is_null($email) AND ! is_null($f_name) AND ! is_null($l_name) AND ! is_null($b_date)) {

                        $check = (new Users)->select('id')
                            ->where('email', $email)
                            ->notWhere('id', Base::userData('id'))
                            ->get();

                        if (empty($check)) {

                            $newData = [
                                'f_name' => $f_name,
                                'l_name' => $l_name,
                                'b_date' => $b_date
                            ];

                            if ($password)
                                $newData['password'] = $password;

                            if (Base::userData('email') !== $email) {
                                $newData['email'] = $email;
                                $newData['status'] = 'passive';
                                $sendLink = true;
                            }

                            $update = (new Users)->where('id', $output->id)->update($newData);

                        } else 
                            $update = false;

                        if ($update) {
                            (new Sessions)->where('user_id', $output->id)->update(['update_session' => 'true']);
                            $alerts[] = [
                                'status' => 'success',
                                'message' => Base::lang('base.save_success')
                            ];
                            $redirect = '/auth/profile';

                            if (isset($sendLink)) {

                                $args = (array) Base::getSession('user');
                                $args['changes'] = '
                                <span style="color: red;">' . Base::userData('email') . '</span> → 
                                <span style="color: green;">' . $email . '</span>';
                                $args = array_merge($args, $newData);
                                (new Notification($this->get()))->add('email_change', $args);

                            }

                        } else {

                            $alerts[] = [
                                'status' => 'warning',
                                'message' => Base::lang('base.save_problem')
                            ];
                        }

                    } else {

                        $alerts[] = [
                            'status' => 'warning',
                            'message' => Base::lang('base.form_cannot_empty')
                        ];

                    }

                }

                break;

            case 'sessions':
                $head = Base::lang('base.sessions');
                $title = $head . ' | ' . $title;
                $description = Base::lang('base.sessions_message');
                $output = [];
                $sessions = new Sessions();

                $session = Base::getSession('user');
                $authCode = Base::authCode();
                if (isset($session->id) !== false) {

                    $records = $sessions->select('id, header, auth_code, ip, last_action_date, last_action_point')
                        ->where('user_id', $session->id)
                        ->getAll();

                    if ($records) {
                        $output = [];
                        foreach ($records as $record) {

                            if (isset($this->get('request')->params['terminate']) !== false AND $record->id == $this->get('request')->params['terminate']) {

                                $delete = $sessions->where('id', $record->id)->delete();
                                if ($authCode != $record->auth_code AND $delete) {
                                    $alerts[] = [
                                        'status' => 'success',
                                        'message' => Base::lang('base.session_terminated')
                                    ];
                                    continue;
                                } else {

                                    $alerts[] = [
                                        'status' => 'warning',
                                        'message' => Base::lang('base.session_not_terminated')
                                    ];
                                }
                            }

                            $record->device = Base::userAgentDetails($record->header);
                            unset($record->header);
                            $output[] = $record;
                        }
                    }
                }

                break;

            case '':
                $head = Base::lang('base.account');
                $description = Base::lang('base.account_message');
                break;
            
            default:
                $head = Base::lang('base.account');
                $description = Base::lang('base.account_message');
                $alerts[] = [
                    'status' => 'warning',
                    'message' => Base::lang('error.page_not_found')
                ];
                $redirect = '/auth';
                $statusCode = 404;
                break;
        }

        $return = [
            'status' => true,
            'statusCode' => $statusCode,
            'arguments' => [
                'title' => $title,
                'head'  => $head,
                'description' => $description,
                'output' => $output,
                'steps' => $steps,
                'action' => $action
            ],
            'alerts' => $alerts,
            'view' => 'user.account'
        ];

        if (isset($session) !== false)
            $return['arguments']['session'] = $session;

        if (isset($authCode) !== false)
            $return['arguments']['auth_code'] = $authCode;

        if (isset($redirect) !== false)
            $return['redirect'] = $redirect;

        return $return;

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

                    $getWithUsername = $users->select('u_name')->where('u_name', $username)->get();
                    if ( !$getWithUsername) {

                        $row = [
                            'u_name'    => $username,
                            'f_name'    => $name,
                            'l_name'    => $surname,
                            'email'     => $email,
                            'password'  => password_hash($password, PASSWORD_DEFAULT),
                            'token'     => Base::tokenGenerator(80),
                            'role_id'   => Base::config('settings.default_user_role'),
                            'created_at'=> time(),
                            'status'    => 'passive'
                        ];

                        $insert = $users->insert($row);

                        if ($insert) {

                            $row['id'] = $insert;
                            (new Notification($this->get()))->add('registration', $row);

                            $alerts[] = [
                                'status' => 'success',
                                'message' => Base::lang('base.registration_successful')
                            ];
                            $redirect = [$this->get()->url('/auth/login'), 4];

                        } else {

                            $alerts[] = [
                                'status' => 'warning',
                                'message' => Base::lang('base.registration_problem')
                            ];

                        }

                    } else {

                        $alerts[] = [
                            'status' => 'warning',
                            'message' => Base::lang('base.username_is_already_used')
                        ];

                    }

                } else {

                    $alerts[] = [
                        'status' => 'warning',
                        'message' => Base::lang('base.email_is_already_used')
                    ];

                }

            } else {

                $alerts[] = [
                    'status' => 'warning',
                    'message' => Base::lang('base.form_cannot_empty')
                ];

            }
            
        }

        $return = [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.register'),
                'description' => Base::lang('base.register_message')
            ],
            'alerts' => $alerts,
            'view' => 'user.register',
        ];

        if (isset($redirect))
            $return['redirect'] = $redirect;

        return $return;

    }

    public function logout() {

        
        $deleteSession = (new Sessions)
            ->where('auth_code', Base::authCode())
            ->delete();

        if ($deleteSession !== false AND $deleteSession !== null) {

            Base::clearSession();
            return [
                'status' => true,
                'alerts' => [[
                    'status' => 'success',
                    'message' => Base::lang('base.signed_out'),
                ]],
                'redirect' => '/',
                'view' => null
            ];

        } else {

            return [
                'status' => false,
                'statusCode' => 401,
                'arguments' => [
                    'title' => Base::lang('err'),
                    'error' => '401',
                    'output' => Base::lang('error.a_problem_occurred') . ' -> (logout)'
                ],
                'view' => ['error', 'error']
            ];
        }

    }

    public function recovery() {

        $alerts = [];
        $step = 1;

        if ($this->get('request')->method === 'POST') {

            extract(Base::input([
                'email' => 'nulled_email',
                'password' => 'nulled_password',
                'token' => 'nulled_text',
            ], $this->get('request')->params));

            if (! is_null($email) AND (is_null($password) AND is_null($token))) { // Step 1: Request 

                $users = (new Users());
                $getUser = $users->select('id, token, status, f_name, u_name, email')
                    ->where('email', $email)
                    ->notWhere('status', 'deleted')
                    ->get();

                if ( ! empty($getUser) ) {

                    if ($getUser->status === 'active') {

                        $sendLink = (new Notification($this->get()))->add('recovery_request', $getUser);
                        if ($sendLink) {

                            $alerts[] = [
                                'status' => 'success',
                                'message' => Base::lang('base.recovery_request_successful')
                            ];
                            $redirect = $this->get()->url('/auth/login');

                        } else {

                            $alerts[] = [
                                'status' => 'warning',
                                'message' => Base::lang('base.recovery_request_problem')
                            ];

                        }

                    } else {

                        $alerts[] = [
                            'status' => 'warning',
                            'message' => Base::lang('base.account_not_verified')
                        ];

                    }

                } else {

                    $alerts[] = [
                        'status' => 'warning',
                        'message' => Base::lang('base.account_not_found')
                    ];

                }

            } elseif (is_null($email) AND (! is_null($password) AND ! is_null($token))) { // Step 3: Reset

                $users = (new Users());
                $getUser = $users->select('id, token, status, f_name, u_name, email')->where('token', $token)->where('status', 'active')->get();
                if (! empty($getUser)) {

                    $update = $users->where('id', $getUser->id)
                        ->update([
                            'password' => $password,
                            'token' => Base::tokenGenerator(80)
                        ]);

                    if ($update) {

                        (new Notification($this->get()))->add('account_recovered', $getUser);
                        $alerts[] = [
                            'status' => 'success',
                            'message' => Base::lang('base.account_recovered')
                        ];
                        $redirect = $this->get()->url('/auth/login');

                    } else {

                        $alerts[] = [
                            'status' => 'warning',
                            'message' => Base::lang('base.account_not_recovered')
                        ];
                    }

                } else {

                    $alerts[] = [
                        'status' => 'error',
                        'message' => Base::lang('base.account_not_found')
                    ];
                    $redirect = $this->get()->url('/auth/recovery');
                }

            } else {

                $alerts[] = [
                    'status' => 'warning',
                    'message' => Base::lang('base.form_cannot_empty')
                ];

            }
            
        } elseif (isset($this->get('request')->params['token']) !== false) { // Step 2: Verify

            extract(Base::input([
                'token' => 'nulled_text',
            ], $this->get('request')->params));

            $users = (new Users());
            $getUser = $users->select('id')->where('token', $token)->where('status', 'active')->get();
            if (! empty($getUser)) {

                $step = 2;

            } else {

                $alerts[] = [
                    'status' => 'error',
                    'message' => Base::lang('base.account_not_found')
                ];
                $redirect = $this->get()->url('/auth/recovery');
            }
        }

        $return = [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.recovery_account'),
                'description' => Base::lang('base.recovery_account_message'),
                'step' => $step,
            ],
            'alerts' => $alerts,
            'view' => 'user.recovery',
        ];

        if (isset($redirect))
            $return['redirect'] = $redirect;

        if (isset($token))
            $return['arguments']['token'] = $token;

        return $return;

    }

}