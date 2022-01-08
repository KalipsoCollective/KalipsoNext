<?php

/**
 * @package KN
 * @subpackage KN Helper
 */

declare(strict_types=1);

namespace App\Helpers;

class KN {


    /**
     * Dump Data
     * @param any $value
     * @param boolean $exit
     * @return void
     */
    public static function dump($value, $exit = false) {

        echo '<pre>';
        var_dump($value);
        echo '</pre>'.PHP_EOL;

        if ($exit) exit;
        
    }


    /**
     * Path
     * @return string $path    main path
     */
    public static function path($dir = null) {
        return KN_ROOT . $dir;
    }


    /**
     * Base URL
     * @param  string|null $body
     * @return string $return
     */
    public static function base($body = null) {

        $url = (self::config('settings.ssl') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/';
        if ($body) $url .= trim(strip_tags($body), '/');

        return $url;
    }


    /**
     * Configuration Parameters
     * @param  string $setting setting value name
     * @return string|null|array|object $return  setting value
     */
    public static function config($setting) {

        global $configs;

        $return = false;
        $settings = false;

        if (strpos($setting, '.') !== false) {
            
            $setting = explode('.', $setting, 2);

            if (isset($configs[$setting[0]]) !== false) {

                $settings = $configs[$setting[0]];

            } else {

                $file = self::path('app/core/config/' . $setting[0] . '.php');
                if (file_exists($file)) {

                    $settings = require $file;
                    $configs[$setting[0]] = $settings;
                    
                }

            }

            if ($settings) {

                $setting = strpos($setting[1], '.') !== false ? explode('.', $setting[1]) : [$setting[1]];

                $data = null;
                foreach ($setting as $key) {
                    
                    if (isset($settings[$key]) !== false) {
                        $data = $settings[$key];
                        $settings = $settings[$key];
                    } else {
                        $data = null;
                    }
                }
                $return = $data;
            }

        }
        
        return $return;

    }


    /**
     * Filter Value
     * @param  any $data
     * @param  string $parameter 
     * @return any $return 
     */
    public static function filter($data = null, $parameter = 'text') {
        /*  parameters
            - text - strip_tags - trim
            - html- htmlspecialchars - trim
            - check - strip_tags - trim ? on : off
            - pass - password_hash - trim
        */
        if (is_array($data))
        {   
            $_value = [];
            foreach ($data as $key => $value)
            {
                if (is_array($value)) {
                    $_value[$key] = self::filter($value, $parameter);
                } else {
                    $value = str_replace('<p><br></p>', '<br>', $value);
                    switch ($parameter)
                    {
                        case 'html': 
                            $_value[$key] = htmlspecialchars(trim($value)); 
                            break;

                        case 'nulled_html': 
                            $_value[$key] = htmlspecialchars(trim($value)); 
                            $_value[$key] = $_value[$key] == '' ? null : $_value[$key]; 
                            if (trim(strip_tags($value)) == '') {
                                $_value[$key] = null;
                            }
                            break;

                        case 'check': 
                            $_value[$key] = !is_null($value) ? 'on' : 'off'; 
                            break;

                        case 'check_as_boolean': 
                            $_value[$key] = !is_null($value) ? true : false; 
                            break;

                        case 'int': 
                            $_value[$key] = (integer)$value; 
                            break;

                        case 'nulled_int': 
                            $_value[$key] = (integer)$value == 0 ? null : (integer)$value; 
                            break;

                        case 'float': 
                            $_value[$key] = floatval($value); 
                            break;

                        case 'pass': 
                            $_value[$key] = password_hash(trim($value), PASSWORD_DEFAULT); 
                            break;

                        case 'nulled_pass': 
                            $_value[$key] = trim($value) != '' ? password_hash(trim($value), PASSWORD_DEFAULT) : null;
                             break;

                        case 'date': 
                            $_value[$key] = strtotime($value. ' 12:00'); 
                            break;

                        case 'nulled_text': 
                            $_value[$key] = strip_tags(trim($value)) == '' ? null : strip_tags(trim($value)); 
                            break;

                        case 'slug': 
                            $_value[$key] = strip_tags(trim($value)) == '' ? null : slugGenerator(strip_tags(trim($value))); 
                            break;

                        default: 
                            $_value[$key] = strip_tags(trim($value));

                    }
                }
                
                if (strpos($parameter, 'nulled') !== false AND $_value[$key] == '') {
                    $_value[$key] = null;
                }
            }
            $data = $_value;
        }
        else
        {
            $data = str_replace('<p><br></p>', '<br>', $data);
            switch ($parameter) {
                case 'html': 
                    $data = htmlspecialchars(trim($data)); 
                    break;

                case 'nulled_html': 
                    $data = htmlspecialchars(trim($data));
                    $data = $data == '' ? null : $data; 
                    if ($data AND trim(strip_tags(htmlspecialchars_decode($data))) == '') {
                        $data = null;
                    }
                    break;

                case 'check': 
                    $data = !is_null($data) ? 'on' : 'off'; 
                    break;

                case 'check_as_boolean': 
                    $data = !is_null($data) ? true : false; 
                    break;

                case 'int': 
                    $data = (integer)$data; 
                    break;

                case 'nulled_int': 
                    $data  = (integer)$data  == 0 ? null : (integer)$data; 
                    break;

                case 'float': 
                    $data = (float)$data; 
                    break;

                case 'pass': 
                    $data = password_hash(trim($data), PASSWORD_DEFAULT); 
                    break;

                case 'nulled_pass': 
                    $data = trim($data) != '' ? password_hash(trim($data), PASSWORD_DEFAULT) : null; 
                    break;

                case 'date': 
                    $data = strtotime($data. ' 12:00'); 
                    break;

                case 'nulled_text': 
                    $data = strip_tags(trim($data)) == '' ? null : strip_tags(trim($data)); 
                    break;

                case 'slug': 
                    $data = strip_tags(trim($data)) == '' ? null : slugGenerator(strip_tags(trim($data))); 
                    break;

                default: 
                    $data = strip_tags(trim($data));
            }

            if (strpos($parameter, 'nulled') !== false AND $data == '') {
                $data = null;
            }
        }

        return $data;

    }


    /**
     * Create Header
     * @param  int|string $code    http status code or different header definitions: powered_by, location, refresh and content_type
     * @param  array $parameters   other parameters are sent as an array. 
     * available keys: write(echo), url(redirect url), second (redirect second), content(content-type)
     * @return void 
     */
    public static function http($code = 200, $parameters = []) {

        /* reference
        $parameters = [
            'write' => '',
            'url' => '',
            'second' => '',
            'content'  => ''
        ]; */

        $httpCodes = [
            200 => 'OK',
            301 => 'Moved Permanently',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error'
        ];

        if (is_numeric($code) AND isset($httpCodes[(int)$code]) !== false) {

            header($_SERVER["SERVER_PROTOCOL"] . ' ' . $code . ' ' . $httpCodes[(int)$code]);

        } else {

            switch ($code) {

                case 'powered_by':
                    header('X-Powered-By: KalipsoNext');
                    break;

                case 'location':
                case 'refresh':
                    if (isset($parameters['url']) === false) {
                        $redirectUrl = isset($_SERVER['HTTP_REFERER']) !== false ? $_SERVER['HTTP_REFERER'] : self::base();
                    } else {
                        $redirectUrl = $parameters['url'];
                    }
                    if (isset($parameters['second']) === false OR (! $parameters['second'] OR ! is_numeric($parameters['second']))) {
                        header('location: ' . $redirectUrl);
                    } else {
                        header('refresh: ' . $parameters['second'] . '; url=' . $redirectUrl);
                    }
                    
                    break;

                case 'content_type':
                    if (isset($parameters['content']) === false) $parameters['content'] = null;

                    switch ($parameters['content']) {
                        case 'application/javascript':
                        case 'js': $parameters['content'] = 'application/javascript'; break;

                        case 'application/zip':
                        case 'zip': $parameters['content'] = 'application/zip'; break;

                        case 'text/plain':
                        case 'txt': $parameters['content'] = 'text/plain'; break;

                        case 'text/xml':
                        case 'xml': $parameters['content'] = 'application/xml'; break;

                        case 'vcf': $parameters['content'] = 'text/x-vcard'; break;

                        default: $parameters['content'] = 'text/html';
                    }
                    header('Content-Type: '.$parameters['content'].'; Charset='.self::config('app.charset'));
                    break;
            }
        }

        if (isset($parameters['write']) !== false) {
            echo $parameters['write'];
        }
    }


    /**
     * View File
     * @param  $file   file to show
     * @return $content    
     */
    public static function view($file, $arguments = []) {

        $file = KN::path('app/resources/view/' . $file . '.php');
        if (file_exists($file)) {

            extract($arguments);
            return require $file;

        } else {
            return null;
        }

    }

    /**
     * Language Translation
     * @param  $key
     * @return $return translated string    
     */
    public static function lang($key) {

        $return = '';
        $return = $key;

        return $return;

    }
}