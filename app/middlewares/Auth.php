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

        if ($type == 'auth' AND isset($_SESSION['user']->id) !== false AND $_SESSION['user']->id) {

            return [
                'status' => true,
            ];

        } elseif ($type == 'nonAuth' AND isset($_SESSION['user']->id) === false) {

            return [
                'status' => true,
            ];

        } else {

            return [
                'status' => false,
                'message' => ($type == 'nonAuth' ? 'you_have_a_session' : 'you_have_not_a_session')
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