<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Helpers\Base;

final class AppController {

    public $request = [];
    public $response = [];

    public function __construct($request = []) {

        $this->request = $request;

    }

    public function index() {

        Base::layout('index');

    }


    public function sandbox() {

        if (Base::config('app.dev_mode')) {
            Base::layout('sandbox', [
                'layout'    => ['layout/header', '_', 'layout/end'],
                'title'     => 'Sandbox | ' . Base::config('app.name'),
                'request'   => $this->request
            ]);
        } else {
            Base::http(301);
            Base::http('location', ['url' => Base::base()]);
        }

    }


    public function dynamicJS() {

        Base::http('content_type', ['content' => 'js']);
        require Base::path('app/resources/script.php');

    }

}