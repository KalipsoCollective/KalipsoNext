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

        return KN::view('user/login');

    }


    public static function register() {

        echo 'Register';

    }

}