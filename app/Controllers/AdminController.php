<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Core\Controller;
use KN\Helpers\Base;
use KN\Core\Model;
use KN\Model\Users;
use KN\Model\UserRoles;
use KN\Model\Sessions;
use KN\Model\Logs;

final class AdminController extends Controller {

    public function dashboard() {

        $users = (new Users)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $userRoles = (new UserRoles)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $sessions = (new Sessions)->select('COUNT(id) as total')->get();
        $logs = (new Logs)->select('COUNT(id) as total')->get();

        $count = [
            'users' => $users->total,
            'user_roles' => $userRoles->total,
            'sessions' => $sessions->total,
            'logs' => $logs->total
        ];

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
                'description' => Base::lang('base.dashboard_message'),
                'count' => $count,
            ],
            'view' => ['admin.dashboard', 'admin']
        ];

    }


    public function users() {

        $users = (new Users)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $userRoles = (new UserRoles)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $sessions = (new Sessions)->select('COUNT(id) as total')->get();
        $logs = (new Logs)->select('COUNT(id) as total')->get();

        $count = [
            'users' => $users->total,
            'user_roles' => $userRoles->total,
            'sessions' => $sessions->total,
            'logs' => $logs->total
        ];

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.users') . ' | ' . Base::lang('base.management'),
                'description' => Base::lang('base.users_message'),
                'count' => $count,
            ],
            'view' => ['admin.users', 'admin']
        ];

    }


    public function userRoles() {

        $users = (new Users)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $userRoles = (new UserRoles)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $sessions = (new Sessions)->select('COUNT(id) as total')->get();
        $logs = (new Logs)->select('COUNT(id) as total')->get();

        $count = [
            'users' => $users->total,
            'user_roles' => $userRoles->total,
            'sessions' => $sessions->total,
            'logs' => $logs->total
        ];

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
                'description' => Base::lang('base.dashboard_message'),
                'count' => $count,
            ],
            'view' => ['admin.dashboard', 'admin']
        ];

    }


    public function sessions() {

        $users = (new Users)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $userRoles = (new UserRoles)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $sessions = (new Sessions)->select('COUNT(id) as total')->get();
        $logs = (new Logs)->select('COUNT(id) as total')->get();

        $count = [
            'users' => $users->total,
            'user_roles' => $userRoles->total,
            'sessions' => $sessions->total,
            'logs' => $logs->total
        ];

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
                'description' => Base::lang('base.dashboard_message'),
                'count' => $count,
            ],
            'view' => ['admin.dashboard', 'admin']
        ];

    }


    public function settings() {

        $users = (new Users)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $userRoles = (new UserRoles)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $sessions = (new Sessions)->select('COUNT(id) as total')->get();
        $logs = (new Logs)->select('COUNT(id) as total')->get();

        $count = [
            'users' => $users->total,
            'user_roles' => $userRoles->total,
            'sessions' => $sessions->total,
            'logs' => $logs->total
        ];

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
                'description' => Base::lang('base.dashboard_message'),
                'count' => $count,
            ],
            'view' => ['admin.dashboard', 'admin']
        ];

    }


    public function logs() {

        $users = (new Users)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $userRoles = (new UserRoles)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
        $sessions = (new Sessions)->select('COUNT(id) as total')->get();
        $logs = (new Logs)->select('COUNT(id) as total')->get();

        $count = [
            'users' => $users->total,
            'user_roles' => $userRoles->total,
            'sessions' => $sessions->total,
            'logs' => $logs->total
        ];

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
                'description' => Base::lang('base.dashboard_message'),
                'count' => $count,
            ],
            'view' => ['admin.dashboard', 'admin']
        ];

    }

}