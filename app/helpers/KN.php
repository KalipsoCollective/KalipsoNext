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
     * @param  $value any
     * @param  $exit boolean
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
     * @return $path main path
     */

    public static function path($dir = null) {
        return KN_ROOT . $dir;
    }


    /**
     * Base URL
     * @param  $body string||null
     * @return $return string
     */

    public static function base($body = null) {

        $url = (self::config('settings.ssl') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/';
        if ($body) $url .= trim(strip_tags($body), '/');

        return $url;
    }


    /**
     * Configuration Parameters
     * @param  string $setting setting value name
     * @return string $return  setting value
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
     * @param  $data any
     * @param  $parameter string
     * @return $return any
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

}