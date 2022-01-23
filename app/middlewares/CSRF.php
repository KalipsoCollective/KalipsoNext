<?php

/**
 * CSRF Middleware
 * 
 **/

namespace App\Middlewares;

use App\Helpers\KN;

class CSRF {


    public function __construct()
    {

    }

    public static function validate($method = 'POST', $args = []) {

        KN::dump($method);
        KN::dump($args, true);

        if ($type == 'auth' AND isset($_SESSION['auth']) !== false AND $_SESSION['auth']) {

            return [
                'status'    => true,
            ];

        } elseif ($type == 'nonAuth' AND (isset($_SESSION['auth']) === false OR ! $_SESSION['auth'])) {

            return [
                'status'    => true,
            ];

        } else {

            return [
                'status'    => false,
                'message'   => ($type == 'nonAuth' ? 'you_have_an_session' : 'you_have_not_an_session')
            ];
        }

    }
}