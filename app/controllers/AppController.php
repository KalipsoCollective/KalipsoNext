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


    public static function sandbox() {

        if (KN::config('app.dev_mode')) {
            KN::layout('sandbox', [
                'layout' => ['layout/header', '_', 'layout/end'],
                'title' => 'Sandbox | ' . KN::config('app.name')
            ]);
        } else {
            KN::http(301);
            KN::http('location', ['url' => KN::base()]);
        }

    }


    public static function dynamicJS() {

        KN::http('content_type', ['content' => 'js']);
        require KN::path('app/resources/script.php');

    }

}