<?php

/**
 * @package KN
 * @subpackage Model
 */

declare(strict_types=1);

namespace App\Model;

use App\Core\DB;
use App\Helpers\KN;

class User {

    public $table = 'users';
    public $sessionTable = 'sessions';
    public $roleTable = 'user_roles';
    public $base = null;
    public $row = null;

    public function __construct() {

        $this->base = (new DB());
        $this->base->table($this->table);

    }

    public function setter($data) {

        if (is_null($this->row)) $this->row = (object)[];

        foreach ($data as $columnName => $columnValue) {
            
            $this->row->{$columnName} = $columnValue;

        }

    }

    public function getUser($with, $data) {

        // single user data
        $get = $this->base->table($this->table)->select(
            'id, u_name, f_name, l_name, email, password, token, role_id, b_date, status'
        );

        if ($with == 'email_or_username') {
            $get->where('u_name', $data)->orWhere('email', $data);
        } elseif ($with == 'email') {
            $get->where('email', $data);
        } elseif ($with == 'u_name') {
            $get->where('u_name', $data);
        } elseif ($with == 'token') {
            $get->where('token', $data);
        } else {
            $get->where('id', $data);
        }

        $get = $get->get();

        if ($get) {

            // role data
            $role = $this->base->table($this->roleTable)->select(
                'name, view_points, action_points'
            )
                ->where('id', $get->role_id)
                ->where('status', 'active')
                ->get();

            if ($role) {

                $get->role_name = $role->name;
                $get->view_points = $role->view_points;
                $get->action_points = $role->action_points;

            }

            $this->setter($get);

        }

        return $this->row;

    }

    public function saveSession($data, $lastActionPoint = null) {

        $getSession = $this->base->table($this->sessionTable)
            ->select('auth_code, id')
            ->where('auth_code', $_COOKIE[KN_SESSION_NAME])
            ->get();

        if ($getSession) {

            // update
            return $this->base->table($this->sessionTable)
                ->where('id', $getSession->id)
                ->update([
                    'user_id'           => $data->id,
                    'header'            => KN::getHeader(),
                    'ip'                => KN::getIp(),
                    'role_id'           => $data->role_id,
                    'last_action_date'  => time(),
                    'last_action_point' => $lastActionPoint
                ]);

        } else {

            // insert
            return $this->base->table($this->sessionTable)
                ->insert([
                    'auth_code'         => $_COOKIE[KN_SESSION_NAME],
                    'user_id'           => $data->id,
                    'header'            => KN::getHeader(),
                    'ip'                => KN::getIp(),
                    'role_id'           => $data->role_id,
                    'last_action_date'  => time(),
                    'last_action_point' => $lastActionPoint
                ]);
        }

    }

    public function clearSession($authCode = null) {

        if (is_null($authCode)) $authCode = $_COOKIE[KN_SESSION_NAME];

        return $this->base->table($this->sessionTable)
            ->where('auth_code', $authCode)
            ->delete();

    }

    public function getSession($authCode, $request = null) {

        $getSession = $this->base->table($this->sessionTable)
            ->select('id, auth_code, user_id, update_session, role_id')
            ->where('auth_code', $authCode)
            ->get();

        $return = null;
        if ($getSession) {

            if ($request) {

                // user data update
                if ($getSession->update_session == 'true') {
                    $return = $this->getUser('id', $getSession->user_id);
                } else {
                    $return = true;
                }

                // update
                $this->base->table($this->sessionTable)
                    ->where('id', $getSession->id)
                    ->update([
                        'role_id'           => isset($return->role_id) !== false ? $return->role_id : $getSession->role_id,
                        'ip'                => KN::getIp(),
                        'header'            => KN::getHeader(),
                        'last_action_date'  => time(),
                        'last_action_point' => $request
                    ]);

            } else {

                $return = $getSession;
            }
        }

        return $return;

    }


    public function addUser($data) {

        return $this->base->table($this->table)
            ->insert($data);

    }

    public function updateUser($update, $id) {

        return $this->base->table($this->table)
            ->where('id', $id)
            ->update($update);

    }

    public function verifyAccount($token) {

        return $this->base->table($this->table)
            ->where('token', $token)
            ->where('status', 'passive')
            ->update([
                'token'     => KN::tokenGenerator(80),
                'status'    => 'active'
            ]);

    }

    public function removeSessions($id) {

        return $this->base->table($this->sessionTable)
            ->where('user_id', $id)
            ->delete();

    }

}