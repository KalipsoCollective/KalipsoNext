<?php

/**
 * @package KN
 * @subpackage Auth Middleware
 */

declare(strict_types=1);

namespace App\Middlewares;

use App\Helpers\KN;

final class Auth {

    public $request = [];

    public function __construct($request = []) {

        $this->request = $request;

    }

    public function with($type = 'auth') {

        KN::dump($_SESSION);

        if ($type == 'auth' AND isset($_SESSION->id) !== false AND $_SESSION['auth']) {

            return [
                'status' => true,
            ];

        } elseif ($type == 'nonAuth' AND (isset($_SESSION['auth']) === false OR ! $_SESSION['auth'])) {

            return [
                'status' => true,
            ];

        } else {

            return [
                'status' => false,
                'message' => ($type == 'nonAuth' ? 'you_have_an_session' : 'you_have_not_an_session')
            ];
        }

    }

    public function view($point) {

        /* (soon)
        $return = false;
        if (
            isset($_SESSION['user']->role->view_points) !== false AND 
            in_array($point, $_SESSION['user']->role->view_points) !== false
        ) {
            $return = true;
        }
        return $return;
        */

    }
}