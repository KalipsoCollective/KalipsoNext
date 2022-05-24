<?php

/**
 * @package KN
 * @subpackage Auth
 */

declare(strict_types=1);

namespace KN\Core;

use KN\Helpers\Base;
use KN\Model\Users;
use KN\Model\UserRoles;
use KN\Model\Sessions;

class Auth {

    /**
     * Factory inheritances 
     **/
    public $request;
    public $response;

    /**
     *  @param object container  factory class   
     *  @return void
     **/

    public function __construct($arguments) {

        $this->request = $arguments['request'];
        $this->response = $arguments['response'];

    }

    public function check() {

        $return = [
            'auth' => false,
            'redirect' => null,
            'alerts' => null
        ];

        $authCode = Base::authCode();
        $dbSession = (new Sessions)->select('id, user_id, update_session')->where('auth_code', $authCode)->get();
        $session = Base::getSession('user');

        if (! empty($dbSession)) {

            /**
             * Sync updated data
             **/
            if (empty($session) OR $dbSession->update_session === 'true') {

                $users = (new Users());
                $getUser = $users->select('id, u_name, f_name, l_name, email, password, token, role_id, b_date, status')
                    ->where('id', $dbSession->user_id)
                    ->get();

                $userRoles = new UserRoles();
                $getUserRole = $userRoles->select('routes, name')->where('id', $getUser->role_id)->get();

                $getUser->role_name = $getUserRole->name;
                $getUser->routes = (object) explode(',', $getUserRole->routes);

                $getUser = Base::privateDataCleaner($getUser);

                Base::setSession($getUser, 'user');
                $return['alerts'] = [
                    'status' => 'success',
                    'message' => Base::lang('base.login_information_updated'),
                ];
                $return['redirect'] = [$this->request->uri, 0];
            }

            /**
             * Update check point 
             **/
            (new Sessions)->where('auth_code', $authCode)
                ->update([
                    'header' => Base::getHeader(),
                    'ip' => Base::getIp(),
                    'last_action_date' => time(),
                    'update_session' => 'false',
                    'last_action_point' => $this->request->uri
                ]);

            $return['auth'] = true;

        } else {

            /**
             * Clear non-functional session data
             **/
            if (! empty($session)) {
                Base::clearSession();
            }
        }

        return $return;

    }

}