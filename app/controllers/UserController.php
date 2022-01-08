<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\KN;

class UserController {


	public function __construct() {

        

    }

    public static function index() {

        echo 'Welcome!';

    }

    public static function login() {

        KN::layout('user/login');

    }


    public static function register() {

        KN::layout('user/register');

    }


    public static function account() {

        KN::layout('user/register');

    }

}