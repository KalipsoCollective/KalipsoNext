<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\KN;

final class AppController {

    public $request = [];

    public function __construct($request = []) {

        $this->request = $request;

    }

    public function index() {

        KN::layout('index');

    }


    public function sandbox() {

        if (KN::config('app.dev_mode')) {
            KN::layout('sandbox', [
                'layout'    => ['layout/header', '_', 'layout/end'],
                'title'     => 'Sandbox | ' . KN::config('app.name'),
                'request'   => $this->request
            ]);
        } else {
            KN::http(301);
            KN::http('location', ['url' => KN::base()]);
        }

    }


    public function dynamicJS() {

        KN::http('content_type', ['content' => 'js']);
        require KN::path('app/resources/script.php');

    }

}