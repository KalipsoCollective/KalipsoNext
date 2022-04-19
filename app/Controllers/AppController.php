<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Controllers\Controller;
use KN\Helpers\Base;
use KN\Model\Model;

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
            $output = '';

            switch ($action) {
                case 'db-init':
                    $head = Base::lang('base.db_init');
                    $title = $head . ' | ' . $title;
                    $description = Base::lang('base.db_init_message');

                    $dbSchema = require Base::path('app/Resources/db_schema.php');

                    if (isset($_GET['start']) !== false) {

                        $init = (new Model)->dbInit($dbSchema);

                        if ($init === 0) {
                            $output .= '<p class="text-success">'.Base::lang('base.db_init_success').'</p>';
                        } else {
                            $output .= '<p class="text-danger">'.str_replace('[ERROR]', $init, Base::lang('base.db_init_problem')).'</p>';
                        }

                    } else {

                        foreach ($dbSchema['tables'] as $table => $detail) {

                            $cols = '
                            <div class="table-responsive">
                                <table class="table table-dark table-sm table-hover table-striped caption-bottom">
                                    <thead>
                                        <tr>
                                            <th scope="col">'.Base::lang('base.column').'</th>
                                            <th scope="col">'.Base::lang('base.type').'</th>
                                            <th scope="col">'.Base::lang('base.auto_inc').'</th>
                                            <th scope="col">'.Base::lang('base.attribute').'</th>
                                            <th scope="col">'.Base::lang('base.default').'</th>
                                            <th scope="col">'.Base::lang('base.index').'</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                            foreach ($detail['cols'] as $col => $colDetail) {

                                $cols .= '
                                        <tr>
                                            <th scope="row">'.$col.'</th>
                                            <td scope="col">
                                                '.$colDetail['type'].(
                                                    isset($colDetail['type_values']) !== false ? 
                                                    (is_array($colDetail['type_values']) ? '('.implode(',', $colDetail['type_values']).')' : 
                                                        '('.$colDetail['type_values']).')' : ''
                                                ).'
                                            </td>
                                            <td scope="col">'.Base::lang('base.' . (isset($colDetail['auto_inc']) !== false ? 'yes' : 'no')).'</td>
                                            <td scope="col">'.(isset($colDetail['attr']) !== false ? $colDetail['attr'] : '').'</td>
                                            <td scope="col">'.(isset($colDetail['default']) !== false ? $colDetail['default'] : '').'</td>
                                            <td scope="col">'.(isset($colDetail['index']) !== false ? $colDetail['index'] : '').'</td>
                                        <tr>';

                            }

                            $tableValues = '';

                            $tableValues = '<h3 class="small text-muted">
                                '.(
                                    isset($dbSchema['table_values']['specific'][$table]['charset']) !== false ? 
                                        Base::lang('base.charset') . ': <strong>'.$dbSchema['table_values']['specific'][$table]['charset'].'</strong><br>' : 
                                        ''
                                ).'
                                '.(
                                    isset($dbSchema['table_values']['specific'][$table]['collate']) !== false ? 
                                        Base::lang('base.collate') . ': <strong>'.$dbSchema['table_values']['specific'][$table]['collate'].'</strong><br>' : 
                                        ''
                                ).'
                                '.(
                                    isset($dbSchema['table_values']['specific'][$table]['engine']) !== false ? 
                                        Base::lang('base.engine') . ': <strong>'.$dbSchema['table_values']['specific'][$table]['engine'].'</strong><br>' : 
                                        ''
                                ).'
                            </h3>';

                            $cols .= '
                                    </tbody>
                                    <caption>'.$tableValues.'</caption>
                                </table>
                            </div>';

                            $output .= '<details><summary>'.$table.'</summary>'.$cols.'</details>';
                        }

                        if ($output != '') {
                            $output = '
                            <h3 class="small text-muted">
                                '.Base::lang('base.db_name').': 
                                <strong>'.Base::config('database.name').'</strong><br>
                                '.Base::lang('base.db_charset').': 
                                <strong>'.(isset($dbSchema['table_values']['charset']) !== false ? $dbSchema['table_values']['charset'] : '-').'</strong><br>
                                '.Base::lang('base.db_collate').': 
                                <strong>'.(isset($dbSchema['table_values']['collate']) !== false ? $dbSchema['table_values']['collate'] : '-').'</strong><br>
                                '.Base::lang('base.db_engine').': 
                                <strong>'.(isset($dbSchema['table_values']['engine']) !== false ? $dbSchema['table_values']['engine'] : '-').'</strong><br>
                            </h3>
                            '.$output.'
                            <p class="small text-danger mt-5">
                                '.str_replace(
                                    [
                                        '[DB_NAME]', 
                                        '[COLLATION]'
                                    ], 
                                    [
                                        '<strong>'.Base::config('database.name').'</strong>',
                                        '<strong>'.Base::config('database.collation').'</strong>'
                                    ], 
                                    Base::lang('base.db_init_alert')
                                ).'
                            </p>
                            <a class="btn btn-light btn-sm" href="'.$this->get()->url('/sandbox/db-init?start').'">
                                '.Base::lang('base.db_init_start').'
                            </a>';
                        }
                    }
                    break;

                case 'db-seed':
                    $head = Base::lang('base.db_seed');
                    $title = $head . ' | ' . $title;
                    $description = Base::lang('base.db_seed_message');

                    if (isset($_GET['start']) !== false) {

                        $output = '<p class="text-muted">Seeding...</p>';
                        $init = (new KN\Core\DB)->dbSeed($dbSchema);

                        if ($init === 0) {
                            $output .= '<p class="text-success">Database has been seeded successfully.</p>';
                        } else {
                            $output .= '<p class="text-danger">There was a problem while seeding the database. -> ' . $init. '</p>';
                        }

                    } else {

                        foreach ($dbSchema['data'] as $table => $detail) {

                            $cols = '
                            <div class="table-responsive">
                                <table class="table table-dark table-sm table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Table</th>
                                            <th scope="col">Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                            foreach ($detail as $tableDataDetail) {

                                $dataList = '<ul class="list-group list-group-flush">';
                                foreach ($tableDataDetail as $col => $data) {
                                    $dataList .= '
                                    <li class="list-group-item d-flex justify-content-between align-items-start space">
                                        <strong>'.$col.'</strong> <span class="ml-2">'.$data.'</span>
                                    </li>';
                                }
                                $dataList .= '</ul>';

                                $cols .= '
                                <tr>
                                    <th scope="row">'.$table.'</th>
                                    <td scope="col">
                                        '.$dataList.'
                                    </td>
                                <tr>';

                            }
                            $cols .= '
                                </table>
                            </div>';

                            $output .= '<details><summary>'.$table.'</summary>'.$cols.'</details>';
                        }

                        if ($output != '') {
                            $output .= '<a class="btn btn-dark mt-5 btn-sm" href="'.self::base('sandbox/db-seed?start').'">Good, Seed!</a>';
                        }
                    }
                    break;

                case 'php-info':
                    $head = Base::lang('base.php_info');
                    $title = $head . ' | ' . $title;
                    $description = Base::lang('base.php_info_message');
                    break;

                case 'session':
                    $head = Base::lang('base.session');
                    $title = $head . ' | ' . $title;
                    $description = Base::lang('base.session_message');
                    break;

                case 'clear-storage':
                    $head = Base::lang('base.clear_storage');
                    $title = $head . ' | ' . $title;
                    $description = Base::lang('base.clear_storage_message');
                    break;
                
                default:
                    $head = Base::lang('base.welcome');
                    $description = Base::lang('base.sandbox_message');
                    break;
            }
            
            return [
                'status' => true,
                'statusCode' => 200,
                'arguments' => [
                    'title' => $title,
                    'head'  => $head,
                    'description' => $description,
                    'output' => $output,
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