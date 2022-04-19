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

            $steps = ['db-init', 'db-seed', 'php-info', 'session', 'clear-storage'];

            $action = '';
            if (
                isset($this->get('request')->attributes['action']) !== false AND 
                in_array($this->get('request')->attributes['action'], $steps))
                $action = $this->get('request')->attributes['action'];

            $title = Base::lang('base.sandbox');

            switch ($action) {
                case 'db-init':
                    $title = Base::lang('base.db_init') . ' | ' . $title;
                    $description = Base::lang('base.db_init_message');
                    break;

                case 'db-seed':
                    $title = Base::lang('base.db_seed') . ' | ' . $title;
                    $description = Base::lang('base.db_seed_message');
                    break;

                case 'php-info':
                    $title = Base::lang('base.php_info') . ' | ' . $title;
                    $description = Base::lang('base.php_info_message');
                    break;

                case 'session':
                    $title = Base::lang('base.session') . ' | ' . $title;
                    $description = Base::lang('base.session_message');
                    break;

                case 'clear-storage':
                    $title = Base::lang('base.clear_storage') . ' | ' . $title;
                    $description = Base::lang('base.clear_storage_message');
                    break;
                
                default:
                    
                    $description = Base::lang('base.sandbox_message');
                    break;
            }
            
            return [
                'status' => true,
                'statusCode' => 200,
                'arguments' => [
                    'title' => $title,
                    'description' => $description,
                    'output' => 'output',
                    'steps' => $steps
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