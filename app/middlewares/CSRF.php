<?php

/**
 * @package KN
 * @subpackage CSRF Middleware
 */

declare(strict_types=1);

namespace App\Middlewares;

use App\Helpers\KN;

final class CSRF {

    public $request = [];

    public function __construct($request = []) {

        $this->request = $request;

    }

    public function validate($method = 'POST') {

        if ($this->request['request_method'] == $method) {

            if (isset($this->request['parameters']['_token']) === false) {

                return [
                    'status'    => false,
                    'message'   => 'csrf_token_mismatch'
                ];

            } elseif (! KN::verifyCSRF($this->request['parameters']['_token'])) {

                return [
                    'status'    => false,
                    'message'   => 'csrf_token_incorrect'
                ];

            } else {

                return [
                    'status' => true,
                ];
            }
        } else {

            return [
                'status' => true,
            ];
        }

    }
}