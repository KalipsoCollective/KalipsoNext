<?php

/**
 * @package KN
 * @subpackage KN Log
 */

declare(strict_types=1);

namespace KN\Core;

use KN\Model\Logs as Model;
use KN\Helpers\Base;

class Log {

    public function add($args) {

        $args = Base::privateDataCleaner($args);
        $exec = microtime(true) - KN_START;
        $request = json_encode($args['request']);
        $response = json_encode($args['response']);

        if (strlen($request) > 2000)
            $request = substr($request, 0, 2000) . '...';

        if (strlen($response) > 2000)
            $response = substr($response, 0, 2000) . '...';

        $model = new Model();
        return $model->insert([
            'endpoint'      => $args['request']->uri,
            'method'        => $args['request']->method,
            'middleware'    => $args['action']->middleware,
            'controller'    => $args['action']->controller,
            'http_status'   => (string) $args['response']->statusCode,
            'auth_code'     => Base::authCode(),
            'ip'            => Base::getIp(),
            'header'        => Base::getHeader(),
            'request'       => $request,
            'response'      => $response,
            'exec_time'     => (string) $exec
        ]);

    }

}