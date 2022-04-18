<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Controllers\Controller;
use KN\Helpers\Base;

final class AppController extends Controller {

    public function index() {

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'title' => Base::lang('base.welcome'),
                'output' => Base::lang('error.welcome_message')
            ]
        ];

    }

    /**
     * It prepares the project files and database, 
     * it is also used for your debugging operations.
     * 
     **/
    public function sandbox() {

        if (Base::config('app.dev_mode')) {

            Base::dump($this->get('routes'));
            
            return [
                'status' => true,
                'statusCode' => 200,
                'arguments' => [
                    'title' => Base::lang('base.sandbox'),
                    'output' => Base::lang('base.sandbox_message')
                ],
                'view' => ['sandbox', 'sandbox']
            ];

        } else {

            return [
                'status' => false,
                'statusCode' => 302,
                'redirect' => '/',
            ];
        }

    }
    /*

    public function dynamicJS() {

        Base::http('content_type', ['content' => 'js']);
        require Base::path('app/resources/script.php');

    }
    */

}