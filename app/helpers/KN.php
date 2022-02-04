<?php

/**
 * @package KN
 * @subpackage KN Helper
 */

declare(strict_types=1);

namespace App\Helpers;

class KN {


    protected static $request = [];
    protected static $response = [];

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

                $settings = $configs[$setting[1]];

            } else {

                $file = self::path('app/config/' . $setting[0] . '.php');
                if (file_exists($file)) {

                    $settings = require $file;
                    $configs[$setting[1]] = $settings;
                    
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
     * Returns Multi-dimensional Form Input Data
     * @param  array $extract    -> variable name => format parameter
     * @param  array $parameter  -> POST, GET or any input resource
     * @return array $return 
     */
    public static function input($extract, $from): array {
        $return = [];
        if (is_array($extract) AND is_array($from))
        {
            foreach ($extract as $key => $value)
            {
                if (isset($from[$key])) $return[$key] = self::filter($from[$key], $value);
                else $return[$key] = self::filter(null, $value);
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

        /**
         *  Available Parameters
         *  
         *     html             ->  trim + htmlspecialchars
         *     nulled_html      ->  trim + htmlspecialchars + if empty string, save as null
         *     check            ->  if empty, save as "off", not "on"
         *     check_as_boolean ->  if empty, save as false, not true
         *     int              ->  convert to integer value (int)
         *     nulled_int       ->  convert to integer value (int) if value is 0 then convert to null
         *     float            ->  convert to float value (floatval())
         *     password         ->  trim + password_hash
         *     nulled_password  ->  trim + password_hash, assign null if empty string
         *     date             ->  strtotime ~ input 12.00(mid day)
         *     nulled_text      ->  strip_tags + trim + if empty string, save as null
         *     slug             ->  strip_tags + trim + slugGenerator
         *     text (default)   ->  strip_tags + trim
         **/

        if (is_array($data)) {

            $_value = [];

            foreach ($data as $key => $value) {

                if (is_array($value)) {
                    $_value[$key] = self::filter($value, $parameter);
                } else {
                    $value = str_replace('<p><br></p>', '<br>', $value);
                    switch ($parameter) {

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
                            $_value[$key] = ! is_null($value) ? 'on' : 'off'; 
                            break;

                        case 'check_as_boolean': 
                            $_value[$key] = ! is_null($value) ? true : false; 
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

                        case 'password': 
                            $_value[$key] = password_hash(trim($value), PASSWORD_DEFAULT); 
                            break;

                        case 'nulled_password': 
                            $_value[$key] = trim($value) != '' ? password_hash(trim($value), PASSWORD_DEFAULT) : null;
                             break;

                        case 'date': 
                            $_value[$key] = strtotime($value. ' 12:00'); 
                            break;

                        case 'nulled_text': 
                            $_value[$key] = trim(strip_tags($value)) == '' ? null : trim(strip_tags($value)); 
                            break;

                        case 'slug': 
                            $_value[$key] = trim(strip_tags($value)) == '' ? null : self::slugGenerator(trim(strip_tags($value))); 
                            break;

                        default: 
                            $_value[$key] = trim(strip_tags($value));

                    }
                }
                
                if (strpos($parameter, 'nulled') !== false AND $_value[$key] == '') {
                    $_value[$key] = null;
                }
            }

            $data = $_value;

        } else {

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
                    $data = ! is_null($data) ? 'on' : 'off'; 
                    break;

                case 'check_as_boolean': 
                    $data = ! is_null($data) ? true : false; 
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

                case 'password': 
                    $data = password_hash(trim($data), PASSWORD_DEFAULT); 
                    break;

                case 'nulled_password': 
                    $data = trim($data) != '' ? password_hash(trim($data), PASSWORD_DEFAULT) : null; 
                    break;

                case 'date': 
                    $data = strtotime($data. ' 12:00'); 
                    break;

                case 'nulled_text': 
                    $data = strip_tags(trim($data)) == '' ? null : strip_tags(trim($data)); 
                    break;

                case 'slug': 
                    $data = strip_tags(trim($data)) == '' ? null : self::slugGenerator(strip_tags(trim($data))); 
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

            header($_SERVER["SERVER_PROTOCOL"] . ' ' . $code . ' ' . $httpCodes[(int) $code]);

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
     * @return void   
     */
    public static function view($file, $arguments = []) {

        $file = KN::path('app/resources/view/' . $file . '.php');
        if (file_exists($file)) {

            extract($arguments);
            require $file;

        } else {
            return null;
        }

    }

    /**
     * User Alert Generator
     * @return string    
     */
    public static function alert() {

        /**
         *   types:
         *   - default
         *   - success
         *   - warning
         *   - error
         **/

        $alert = '';

        if (isset(self::$response['messages']) !== false AND count(self::$response['messages'])) {

            $iconComponent = require KN::path('app/resources/view/components/icons.php');

            if (file_exists($file = KN::path('app/resources/view/components/alert.php'))) {
                $alertComponent = require $file;
            } else {
                $alertComponent = [
                    'component' => '<div class="kn--message [CLASS]">[ICON] [TITLE] [MESSAGE]</div>',
                    'classes'   => [
                        'default'   => '', 
                        'success'   => 'kn--message-success', 
                        'alert'   => 'kn--message-warning', 
                        'error'     => 'kn--message-error'
                    ]
                ];
            }

            $component = $alertComponent['component'];
            $classes = $alertComponent['classes'];
            
            foreach (self::$response['messages'] as $m) {

                $title = isset($m['title']) !== false ? '<strong>' . $m['title'] . '</strong>' : '';
                $message = isset($m['message']) !== false ? $m['message'] : '';
                $icon = isset($m['icon']) !== false ? $m['icon'] : '';
                $status = in_array($m['status'], ['success', 'error', 'alert']) !== false ? $m['status'] : 'default';


                $class = $classes[$status];

                // Material Design Icons
                if ($icon == '') {

                    switch ($status) {
                        case 'success':
                            $icon = $iconComponent[$status];
                            break;

                        case 'error':
                             $icon = $iconComponent[$status];
                            break;

                         case 'alert':
                             $icon = $iconComponent[$status];
                            break;
                        
                        default:
                            $icon = $iconComponent['info'];
                            break;
                    }

                }

                $icon = '<span class="'.$icon.'"></span>';

                $alert .= str_replace(
                    ['[CLASS]', '[ICON]', '[TITLE]', '[MESSAGE]'], 
                    [$class, $icon, $title, $message],
                    $component
                );

            }


        }

        return $alert;

    }

    /**
     * Layout Creator
     * @param  $file   body file
     * @return void    
     */
    public static function layout($file, $externalParams = []) {

        $title = isset($externalParams['title']) !== false ? $externalParams['title'] : self::config('settings.name');
        $layout = isset($externalParams['layout']) !== false ? $externalParams['layout'] : [
            'layout/header', 
            'layout/nav', 
            '_', 
            'layout/footer', 
            'layout/end'
        ];

        $arguments = [
            'title' => $title,
        ];

        if (isset($externalParams['request']) !== false) self::$request = $externalParams['request'];
        if (isset($externalParams['response']) !== false) self::$response = $externalParams['response'];

        if (isset($externalParams['arguments']) !== false AND is_array($externalParams['arguments'])) {
            $arguments = array_merge($arguments, $externalParams['arguments']);
        }

        if (isset(self::$response['redirect']) !== false) {

            self::http('refresh', [
                'second'    => self::$response['redirect'][0],
                'url'       => self::$response['redirect'][1]
            ]);

        }

        foreach ($layout as $part) {

            if ($part == '_') {
                $part = $file;
            }

            self::view($part, $arguments);
            echo PHP_EOL;

        }

    }


    /**
     * Language Translation
     * @param  $key 
     * @return $key translated string    
     */
    public static function lang($key) {

        global $languageFile;

        if (isset($languageFile[$key]) !== false) {

            $key = $languageFile[$key];

        }

        return $key;

    }


    /**
     * Assets File Controller
     * @param string $filename
     * @param bool $version
     * @param bool $tag
     * @param bool $echo
     * @param array $externalParameters
     * @return string|null
     */
    public static function assets(string $filename, $version = true, $tag = false, $echo = false, $externalParameters = []) {

        $fileDir = rtrim( self::path().'assets/'.$filename, '/' );
        $return = trim( self::base().'assets/'.$filename, '/' );
        if (file_exists( $fileDir )) {

            $return = $version==true ? $return.'?v='.filemtime($fileDir) : $return;
            if ( $tag==true ) // Only support for javascript and stylesheet files
            {
                $_externalParameters = '';
                foreach ($externalParameters as $param => $val) {
                    $_externalParameters = ' ' . $param . '="' . $val . '"';
                }

                $file_data = pathinfo( $fileDir );
                if ( $file_data['extension'] == 'css' )
                {
                    $return = '<link'.$_externalParameters.' rel="stylesheet" href="'.$return.'" type="text/css"/>'.PHP_EOL.'       ';

                } elseif ( $file_data['extension'] == 'js' )
                {
                    $return = '<script'.$_externalParameters.' src="'.$return.'"></script>'.PHP_EOL.'       ';
                }
            }

        } else {
            $return = null;
            // new app\core\Log('sys_asset', $filename);
        }

        if ( $echo == true ) {

            echo $return;
            return null;

        } else {
            return $return;
        }

    }

    /**
     * CSRF Token Generator
     * @param bool $onlyToken  Output option
     * @return string|null
     */
    public static function createCSRF($onlyToken = false) {

        global $requestUri;

        $return = null;
        if (isset($_COOKIE[KN_SESSION_NAME]) !== false) {

            $csrf = [
                'cookie'        => $_COOKIE[KN_SESSION_NAME],
                'timeout'       => strtotime('+1 hour'),
                'request_uri'   => $requestUri,
                'header'        => self::getHeader(),
                'ip'            => self::getIp()
            ];

            $return = self::encryptKey(json_encode($csrf));

            if (! $onlyToken) {
                $return = '<input type="hidden" name="_token" value="'.$return.'">';
            }
        }
        return $return;

    }


    /**
     * CSRF Token Verifier
     * @param string $token  Token
     * @return bool
     */
    public static function verifyCSRF($token) {

        global $requestUri;

        $return = false;
        $token = @json_decode(self::decryptKey($token), true);
        if (is_array($token)) {

            if (
                (isset($token['cookie']) !== false AND $token['cookie'] == $_COOKIE[KN_SESSION_NAME]) AND
                (isset($token['timeout']) !== false AND $token['timeout'] >= time()) AND
                (isset($token['request_uri']) !== false AND $requestUri) AND
                (isset($token['header']) !== false AND $token['header'] == self::getHeader()) AND
                (isset($token['ip']) !== false AND $token['ip'] == self::getIp())

            ) {
                $return = true;
            }

        }

        return $return;

    }

    /**
     * Current Page Class
     * @param string|null $route  Route
     * @return string|void
     */
    public static function currentPage($route = null) {

        global $requestUri;

        if (is_null($route) AND $requestUri == '') {
            return ' active';
        } elseif (! is_null($route) AND trim($route, '/') == $requestUri) {
            return ' active';
        }

    }


    /**
     * Session Starter
     * Assign to session name and start session
     * @return void
     */
    public static function sessionStart() {

        session_name(KN_SESSION_NAME);
        session_start();

    }

    /**
     * Get Session
     * Return all session information or specific data.
     * @param string $key   specific key
     * @return bool|string|array|null
     */
    public static function getSession($key = null) {
        
        $return = null;
        if (isset($_SESSION) !== false) {

            if (is_string($key) AND isset($_SESSION[$key]) !== false) {
                $return = $_SESSION[$key];
            } elseif(is_null($key)) {
                $return = $_SESSION;
            }

        }
        return $return;

    }

    /**
     * Set Session
     * Set to all session information or specific data.
     * @param any $data   data
     * @param string $key   specific key
     * @return bool
     */
    public static function setSession($data = null, $key = null) {

        if (is_string($key)) {
            $_SESSION[$key] = $data;
        } else {

            if (isset($data->password) !== false) {
                unset($data->password);
            }

            $_SESSION = $data;
        }

        return $_SESSION;

    }


    /**
     * Get IP Adress
     * @return string
     */
    public static function getIp() {

        if (getenv("HTTP_CLIENT_IP")) { 
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {

            $ip = getenv("HTTP_X_FORWARDED_FOR");
            if (strpos($ip, ',')) {
                $tmp = explode(',', $ip);
                $ip = trim($tmp[0]);
            }
        } else {
            $ip = getenv("REMOTE_ADDR");
        }

        return $ip == '::1' ? '127.0.0.1' : $ip;

    }


    /**
     * Get Header
     * @return string
     */
    public static function getHeader() {

        return isset($_SERVER['HTTP_USER_AGENT']) !== false ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';

    }


    /**
     * Format File Size
     * @param $bytes
     * @return string
     */

    public static function formatSize($bytes) {

        if ($bytes >= 1073741824) $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        elseif ($bytes >= 1048576) $bytes = number_format($bytes / 1048576, 2) . ' MB';
        elseif ($bytes >= 1024) $bytes = number_format($bytes / 1024, 2) . ' KB';
        elseif ($bytes > 1) $bytes = $bytes . ' ' . self::lang('byte') . lang('plural_suffix');
        elseif ($bytes == 1) $bytes = $bytes . ' ' . self::lang('byte');
        else $bytes = '0 ' . self::lang('byte');

        return $bytes;

    }


    /**
     * Get User Device Details
     * @param string|null $ua
     * @return array
     */

    public static function userAgentDetails($ua = null): array {

        $ua = is_null($u) ? self::getHeader() : $ua;
        $browser = '';
        $platform = '';
        $bIcon = 'mdi mdi-close-circle';
        $pIcon = 'mdi mdi-close-circle';

        $browserList = [
            'Trident\/7.0'          => ['Internet Explorer 11','mdi mdi-internet-explorer'],
            'MSIE'                  => ['Internet Explorer','mdi mdi-internet-explorer'],
            'Edge'                  => ['Microsoft Edge','mdi mdi-microsoft-edge-legacy'],
            'Edg'                   => ['Microsoft Edge','mdi mdi-microsoft-edge'],
            'Internet Explorer'     => ['Internet Explorer','mdi mdi-internet-explorer'],
            'Beamrise'              => ['Beamrise','mdi mdi-earth'],
            'Opera'                 => ['Opera','mdi mdi-opera'],
            'OPR'                   => ['Opera','mdi mdi-opera'],
            'Vivaldi'               => ['Vivaldi','mdi mdi-earth'],
            'Shiira'                => ['Shiira','mdi mdi-earth'],
            'Chimera'               => ['Chimera','mdi mdi-earth'],
            'Phoenix'               => ['Phoenix','mdi mdi-earth'],
            'Firebird'              => ['Firebird','mdi mdi-earth'],
            'Camino'                => ['Camino','mdi mdi-earth'],
            'Netscape'              => ['Netscape','mdi mdi-earth'],
            'OmniWeb'               => ['OmniWeb','mdi mdi-earth'],
            'Konqueror'             => ['Konqueror','mdi mdi-earth'],
            'icab'                  => ['iCab','mdi mdi-earth'],
            'Lynx'                  => ['Lynx','mdi mdi-earth'],
            'Links'                 => ['Links','mdi mdi-earth'],
            'hotjava'               => ['HotJava','mdi mdi-earth'],
            'amaya'                 => ['Amaya','mdi mdi-earth'],
            'MiuiBrowser'           => ['MIUI Browser','mdi mdi-earth'],
            'IBrowse'               => ['IBrowse','mdi mdi-earth'],
            'iTunes'                => ['iTunes','mdi mdi-earth'],
            'Silk'                  => ['Silk','mdi mdi-earth'],
            'Dillo'                 => ['Dillo','mdi mdi-earth'],
            'Maxthon'               => ['Maxthon','mdi mdi-earth'],
            'Arora'                 => ['Arora','mdi mdi-earth'],
            'Galeon'                => ['Galeon','mdi mdi-earth'],
            'Iceape'                => ['Iceape','mdi mdi-earth'],
            'Iceweasel'             => ['Iceweasel','mdi mdi-earth'],
            'Midori'                => ['Midori','mdi mdi-earth'],
            'QupZilla'              => ['QupZilla','mdi mdi-earth'],
            'Namoroka'              => ['Namoroka','mdi mdi-earth'],
            'NetSurf'               => ['NetSurf','mdi mdi-earth'],
            'BOLT'                  => ['BOLT','mdi mdi-earth'],
            'EudoraWeb'             => ['EudoraWeb','mdi mdi-earth'],
            'shadowfox'             => ['ShadowFox','mdi mdi-earth'],
            'Swiftfox'              => ['Swiftfox','mdi mdi-earth'],
            'Uzbl'                  => ['Uzbl','mdi mdi-earth'],
            'UCBrowser'             => ['UCBrowser','mdi mdi-earth'],
            'Kindle'                => ['Kindle','mdi mdi-earth'],
            'wOSBrowser'            => ['wOSBrowser','mdi mdi-earth'],
            'Epiphany'              => ['Epiphany','mdi mdi-earth'],
            'SeaMonkey'             => ['SeaMonkey','mdi mdi-earth'],
            'Avant Browser'         => ['Avant Browser','mdi mdi-earth'],
            'Chrome'                => ['Google Chrome','mdi mdi-google-chrome'],
            'CriOS'                 => ['Google Chrome','mdi mdi-google-chrome'],
            'Safari'                => ['Safari','mdi mdi-apple-safari'],
            'Firefox'               => ['Firefox','mdi mdi-firefox'],
            'Mozilla'               => ['Mozilla','mdi mdi-firefox']
        ];

        $platformList = [
            'windows'               => ['Windows','mdi mdi-microsoft-windows'],
            'iPad'                  => ['iPad','mdi mdi-apple'],
            'iPod'                  => ['iPod','mdi mdi-apple'],
            'iPhone'                => ['iPhone','mdi mdi-apple'],
            'mac'                   => ['Apple MacOS','mdi mdi-apple'],
            'android'               => ['Android','mdi mdi-android'],
            'linux'                 => ['Linux','mdi mdi-linux'],
            'Nokia'                 => ['Nokia','mdi mdi-microsoft'],
            'BlackBerry'            => ['BlackBerry','mdi mdi-blackberry'],
            'FreeBSD'               => ['FreeBSD','mdi mdi-freebsd'],
            'OpenBSD'               => ['OpenBSD','mdi mdi-linux'],
            'NetBSD'                => ['NetBSD','mdi mdi-linux'],
            'UNIX'                  => ['UNIX','mdi mdi-mouse'],
            'DragonFly'             => ['DragonFlyBSD','mdi mdi-linux'],
            'OpenSolaris'           => ['OpenSolaris','mdi mdi-linux'],
            'SunOS'                 => ['SunOS','mdi mdi-linux'],
            'OS\/2'                 => ['OS/2','mdi mdi-mouse'],
            'BeOS'                  => ['BeOS','mdi mdi-mouse'],
            'win'                   => ['Windows','mdi mdi-windows'],
            'Dillo'                 => ['Linux','mdi mdi-linux'],
            'PalmOS'                => ['PalmOS','mdi mdi-mouse'],
            'RebelMouse'            => ['RebelMouse','mdi mdi-mouse']
        ];

        foreach($browserList as $pattern => $name) {
            if ( preg_match("/".$pattern."/i",$ua, $match)) {
                $bIcon = $name[1];
                $browser = $name[0];
                $known = ['Version', $pattern, 'other'];
                $patternVersion = '#(?<browser>' . join('|', $known).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
                preg_match_all($patternVersion, $ua, $matches);
                $i = count($matches['browser']);
                if ($i != 1) {
                    if (strripos($ua,"Version") < strripos($ua,$pattern)){
                        $version = @$matches['version'][0];
                    }
                    else {
                        $version = @$matches['version'][1];
                    }
                }
                else {
                    $version = @$matches['version'][0];
                }
                break;
            }
        }

        foreach($platformList as $key => $platform) {
            if (stripos($ua, $key) !== false) {
                $pIcon = $platform[1];
                $platform = $platform[0];
                break;
            }
        }

        $browser = $browser == '' ? self::lang('undetected') : $browser;
        $platform = $platform == '' ? self::lang('undetected') : $platform;

        $osPatterns = [
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        ];

        foreach ($osPatterns as $regex => $value) {
            if (preg_match($regex, $ua))
            {
                $osPlatform = $value;
            }
        }

        $version = empty($version) ? '' : 'v'.$version;
        $osPlatform = isset($osPlatform) === false ? self::lang('undetected') : $osPlatform;

        return [
            'user_agent'=> $ua,         // User Agent
            'browser'   => $browser,    // Browser Name
            'version'   => $version,    // Version
            'platform'  => $platform,   // Platform
            'os'        => $osPlatform, // Platform Detail
            'b_icon'    => $bIcon,      // Browser Icon(icon class name like from Material Design Icon)
            'p_icon'    => $pIcon       // Platform Icon(icon class name like from Material Design Icon)
        ];

    }


    /**
     * Get String to Slug
     * @param string $str
     * @param array $options
     * @return string
     */
    public static function slugGenerator($str, $options=[]): string {

        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
        $defaults = [
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => [],
            'transliterate' => true
        ];
        $options = array_merge($defaults, $options);
        $charMap = [
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ő' => 'O',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ő' => 'o',
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',
            // Latin symbols
            '©' => '(c)',
            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',
            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
            // Czech
            'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Ť' => 'T', 'Ů' => 'U', 'ď' => 'd', 'ě' => 'e',
            'ň' => 'n', 'ř' => 'r', 'ť' => 't', 'ů' => 'u',
            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',
            // Latvian
            'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
            'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z'
        ];
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
        if ($options['transliterate']) {
            $str = str_replace(array_keys($charMap), $charMap, $str);
        }
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
        $str = trim($str, $options['delimiter']);
        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;

    }


    /**
     * String Transformator
     * @param string $type
     * @param string $data
     * @return string
     */
    public static function stringTransform($type, $data = ''): string {

        switch ($type) {

            case 'uppercasewords':
            case 'ucw':
                $data = Transliterator::create("Any-Title")->transliterate($data);
                break;

            case 'uppercasefirst':
            case 'ucf':
                $data = Transliterator::create("Any-Title")->transliterate($data);
                $data = explode(' ', $data);
                if (count($data)>1) {

                    $_data = [ 0 => $data[0] ];
                    foreach ($data as $index => $text) {

                        if ($index) {

                            $_data[$index] = stringTransform('l', $text);

                        }
                    }
                    $data = implode(' ', $_data);
                } else {
                    $data = implode(' ', $data);
                }
                break;

            case 'lowercase':
            case 'l':
                $data = Transliterator::create("Any-Lower")->transliterate($data);
                break;

            case 'uppercase':
            case 'u':
                $data = Transliterator::create("Any-Upper")->transliterate($data);
                break;
        }

        return $data;

    }


    /**
     * Data Encrypter
     * @param string $text
     * @return string
     */
    public static function encryptKey($text): string {

        $ciphering = "AES-128-CTR";
        $encryptionIv = '1234567891011121';
        $encryptionKey = md5(self::config('app.name'));
        return openssl_encrypt($text, $ciphering,
            $encryptionKey, 0, $encryptionIv);

    }


    /**
     * Data Decrypter
     * @param string $encryptedString
     * @return string
     */
    public static function decryptKey($encryptedString): string {

        $ciphering = "AES-128-CTR";
        $decryptionIv = '1234567891011121';
        $options = 0;
        $decryptionKey = md5(self::config('app.name'));
        return openssl_decrypt ($encryptedString, $ciphering,
            $decryptionKey, 0, $decryptionIv);

    }

    /**
     * Write the value of the submitted field.
     * @param string $name
     * @return string
     */
    public static function inputValue($name) {

        $return = '';
        if (isset(self::$request['parameters'][$name]) !== false) {
            $return = 'value="' . self::$request['parameters'][$name] . '"';
        }
        return $return;

    }

    /**
     * Get URL attribute
     * @param string $name
     * @return string
     */
    public static function getAttribute($name) {

        $return = null;
        if (isset(self::$request['attributes'][$name]) !== false) {
            $return = self::$request['attributes'][$name];
        }
        return $return;

    }

}