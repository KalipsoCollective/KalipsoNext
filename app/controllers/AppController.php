<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\KN;

class AppController {


	public function __construct() {

        

    }

    public static function index() {

        KN::layout('index');

    }


    public static function dynamicJS() {

        KN::http('content_type', ['content' => 'js']);
        require KN::path('app/resources/script.php');

    }

}