<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\KN;

final class UserController {

    public $request = [];

    public function __construct($request = []) {

        $this->request = $request;

    }

    public function index() {

        echo 'Welcome!';

    }

    public function login() {


        if ($this->request['request_method'] == 'POST') {

            $username = $this->request['parameters']['username'];
            $password = $this->request['parameters']['username'];
        }

        KN::layout('user/login', [
            'title'     => KN::lang('login') . ' | ' . KN::config('app.name'),
            'request'   => $this->request
        ]);

    }


    public static function register() {

        KN::layout('user/register');

    }


    public static function account() {

        KN::layout('user/register');

    }

}