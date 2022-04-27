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
                            $getUserRole = $userRoles->select('view_points, action_points, name')->where('id', $getUser->role_id)->get();

                            if (! empty($getUserRole)) {

                                $getUser->role_name = $getUserRole->name;
                                $getUser->view_points = (object) explode(',', $getUserRole->view_points);
                                $getUser->action_points = (object) explode(',', $getUserRole->action_points);

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

        if (
            isset($this->get('request')->attributes['action']) !== false AND 
            in_array($this->get('request')->attributes['action'], array_keys($steps)))
            $action = $this->get('request')->attributes['action'];

        $title = Base::lang('base.account');
        $output = '';
        $alerts = [];

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

                        $update = [
                            'f_name' => $f_name,
                            'l_name' => $l_name,
                            'b_date' => $b_date,
                            'email' => $email
                        ];

                        if ($password)
                            $update['password'] = $password;

                        $update = (new Users)->where('id', $output->id)->update($update);

                        if ($update) {

                            (new Sessions)->where('user_id', $output->id)->update(['update_session' => 'true']);
                            $alerts[] = [
                                'status' => 'success',
                                'message' => Base::lang('base.save_success')
                            ];
                            $redirect = '/auth/profile';

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

            
            default:
                $head = Base::lang('base.account');
                $description = Base::lang('base.account_message');
                break;
        }

        $return = [
            'status' => true,
            'statusCode' => 200,
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
                'redirect' => '/'
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

}