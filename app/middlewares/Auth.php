<?php

/**
 * @package KN
 * @subpackage Auth Middleware
 */

declare(strict_types=1);

namespace App\Middlewares;

use App\Helpers\KN;
use App\Model\User as UserModel;

final class Auth {

    public $request = [];

    public function __construct($request = []) {

        $this->request = $request;

    }

    public function with($type = 'auth') {

        $return = [];

        if ($type == 'auth' AND isset($_SESSION['user']->id) !== false AND $_SESSION['user']->id) {

            $return = [
                'status' => true
            ];

        } elseif ($type == 'nonAuth' AND isset($_SESSION['user']->id) === false) {

            $return = [
                'status' => true,
            ];

        } else {

            $return = [
                'status' => false,
            ];
        }

        if (! $return['status']) {
            $return['message'] = ($type == 'nonAuth' ? 'you_have_a_session' : 'you_have_not_a_session');
        }

        return $return;

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

    public function verify() {

        $return = ['status' => true];

        if (isset($_GET['verify-account']) !== false) {

            $verifyToken = KN::filter($_GET['verify-account'], 'nulled_text');
            if ($verifyToken) {

                $verify = (new UserModel())->verifyAccount($verifyToken);
                if ($verify) {
                    $return['message'] = [
                        'status' => 'success', 
                        'title' => 'success',
                        'message' => 'your_account_verified'
                    ];
                } else {
                    $return['message'] = [
                        'status' => 'error', 
                        'title' => 'error',
                        'message' => 'your_account_not_verified'
                    ];
                }
            }
            

        }

        /*
        

        KN::input([]);

        $return['message'] = ($type == 'nonAuth' ? 'you_have_a_session' : 'you_have_not_a_session');
        */

        return $return;

    }
}