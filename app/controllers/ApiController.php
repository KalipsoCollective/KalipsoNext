<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\KN;

final class ApiController {

    public $request = [];
    public $response = [];

    public function __construct($request = []) {

        $this->request = $request;

    }

    public static function index() {

        KN::http('content_type', 
            [   
                'content' => 'json',
                'write' => json_encode(["VERSION" => KN_VERSION])
            ]
        );

    }

}