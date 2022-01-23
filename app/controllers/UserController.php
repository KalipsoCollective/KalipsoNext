<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\KN;

final class UserController {

    public static function index() {

        echo 'Welcome!';

    }

    public static function login($args) {


        KN::dump($args);
        
        KN::layout('user/login', [
            'title'     => KN::lang('login') . ' | ' . KN::config('app.name'),
            'arguments' => $args
        ]);

    }


    public static function register() {

        KN::layout('user/register');

    }


    public static function account() {

        KN::layout('user/register');

    }

}