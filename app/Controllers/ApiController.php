<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Helpers\Base;

final class ApiController {

    public $request = [];
    public $response = [];

    public function __construct($request = []) {

        $this->request = $request;

    }

    public static function index() {

        Base::http('content_type', 
            [   
                'content' => 'json',
                'write' => json_encode(["VERSION" => KN_VERSION])
            ]
        );

    }

}