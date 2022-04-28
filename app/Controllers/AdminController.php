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

final class AdminController extends Controller {

    public function dashboard() {

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
                'output' => Base::lang('base.dashboard_message')
            ],
            'view' => ['admin.dashboard', 'admin']
        ];

    }


    public function users() {

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
                'output' => Base::lang('base.dashboard_message')
            ],
            'view' => ['admin.dashboard', 'admin']
        ];

    }


    public function userRoles() {

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
                'output' => Base::lang('base.dashboard_message')
            ],
            'view' => ['admin.dashboard', 'admin']
        ];

    }


    public function sessions() {

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
                'output' => Base::lang('base.dashboard_message')
            ],
            'view' => ['admin.dashboard', 'admin']
        ];

    }


    public function settings() {

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
                'output' => Base::lang('base.dashboard_message')
            ],
            'view' => ['admin.dashboard', 'admin']
        ];

    }


    public function logs() {

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
                'output' => Base::lang('base.dashboard_message')
            ],
            'view' => ['admin.dashboard', 'admin']
        ];

    }

}