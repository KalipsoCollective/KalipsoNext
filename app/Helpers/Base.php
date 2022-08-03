<?php

/**
 * @package KN
 * @subpackage KN Helper
 */

declare(strict_types=1);

namespace KN\Helpers;

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use \FilesystemIterator;

class Base {

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
     * Get the directory size
     * @param  string $directory
     * @return integer
     */
    public static function dirSize($directory) {
        $size = 0;
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file)
        {
            if ($file->getFilename() == '..' OR $file->getFilename() == '.gitignore') 
                continue;

            $size+=$file->getSize();
        }
        return $size;
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
     * Generate URL
     * @param  string|null $body
     * @return string $return
     */
    public static function generateURL($module, $parameter = null) {

        $body = '';
        if (strpos($module, '_') !== false) {

            $menuController = (new \KN\Controllers\MenuController((object)['request'=>'']));
            $module = explode('_', $module, 2);
            $body = $menuController->urlGenerator($module[0], $module[1], $parameter);
        }

        return self::base($body);
    }

    /**
     *  Dynamic URL Generator
     *  @param string $route
     *  @param array $attributes
     *  @return string $url
     **/
    public static function dynamicURL($route, $param = []) {

        foreach ($param as $attr => $value) {
            $route = str_replace(':' . $attr, $value, $route);
        }
        return $route;
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

                $file = self::path('app/Resources/config/' . $setting[0] . '.php');
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
        
        return is_string($return) ? html_entity_decode($return) : $return;

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
                if (isset($from[$key]) !== false) $return[$key] = self::filter($from[$key], $value);
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
         *     nulled_text      ->  strip_tags + trim + htmlentities + if empty string, save as null
         *     nulled_email     ->  strip_tags + trim + filter_var@FILTER_VALIDATE_EMAIL + if empty string, save as null
         *     slug             ->  strip_tags + trim + slugGenerator
         *     text (default)   ->  strip_tags + trim + htmlentities
         *     script           ->  preg_replace for script tags
         *     color            ->  regex hex
         **/
        if (is_array($data)) {
            $_data = [];
            foreach ($data as $key => $value) {
                $_data[$key] = self::filter($value, $parameter);
            }
            $data = $_data;
        } else {

            switch ($parameter) {

                case 'html': 
                case 'nulled_html':
                    $data = htmlspecialchars(trim((string)$data)); 
                    if ($parameter === 'nulled_html' AND trim(strip_tags(htmlspecialchars_decode((string)$data))) === '') {
                        $data = null;
                    }
                    break;

                case 'check':
                case 'check_as_boolean':

                    $data = ($data) ? 'on' : 'off'; 
                    if ($parameter === 'check_as_boolean')
                        $data = $data === 'on' ? true : false;

                    break;

                case 'int':
                case 'nulled_int': 
                    $data = (integer)$data; 
                    if ($parameter === 'nulled_int')
                        $data = empty($data) ? null : $data;
                    break;

                case 'float': 
                    $data = (float)$data; 
                    break;

                case 'password':
                case 'nulled_password':
                    $data = password_hash(trim((string)$data), PASSWORD_DEFAULT); 
                    if ($parameter === 'nulled_password') {
                        $data = empty($data) ? null : $data;
                    }
                    break;

                case 'date': 
                    $data = strtotime($data . ' 12:00'); 
                    break;

                case 'nulled_email':
                    $data = empty($data) ? null : strip_tags(trim((string)$data));
                    if ($data) $data = filter_var($data, FILTER_VALIDATE_EMAIL) ? $data : null; 
                    break;

                case 'slug': 
                    $data = empty($data) ? null : self::slugGenerator(strip_tags(trim((string)$data))); 
                    break;

                case 'script': 
                    $data = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '-', $data); 
                    break;

                case 'color': 
                    if( ! preg_match('/^#[a-f0-9]{6}$/i', $data) ) {
                        $data = '#000000';
                    }
                    break;

                default: 
                    $data = htmlentities(trim(strip_tags((string)$data)), ENT_QUOTES);
                    if ($parameter === 'nulled_text')
                        $data = empty($data) ? null : $data;
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
            302 => 'Found',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable'
        ];

        if (is_numeric($code) AND isset($httpCodes[(int)$code]) !== false) {

            header($_SERVER["SERVER_PROTOCOL"] . ' ' . $code . ' ' . $httpCodes[(int) $code]);

        } else {

            switch ($code) {

                case 'powered_by':
                    header('X-Powered-By: KalipsoNext/v' . KN_VERSION);
                    break;

                case 'retry_after':
                    header('Retry-After: ' . (isset($parameters['second']) !== false ? $parameters['second'] : 5));
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
                        case 'application/json':
                        case 'json': $parameters['content'] = 'application/json'; break;
                        
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
     * User Alert Generator
     * @param array $alerts alert arguments
     * @return string    
     */
    public static function alert(array $alerts = []) {

        /**
         *   types:
         *   - default
         *   - success
         *   - warning
         *   - error
         **/

        $alert = '';
        foreach ($alerts as $a) {

            switch ($a['status']) {
                case 'error':
                    $a['status'] = 'danger';
                    break;
                
                case 'default':
                    $a['status'] = 'dark';
                    break;
            }

            $alert .= '<div class="alert alert-' . $a['status'] . '">' . $a['message'] . '</div>';

        }

        return $alert;

    }

    /**
     * Stored User Alert Generator
     * @param array externalAlerts  external alerts together session stored alerts
     * @return string    
     */
    public static function sessionStoredAlert($externalAlerts = []) {

        $alerts = is_array($externalAlerts) ? $externalAlerts : [];
        if (isset($_SESSION['alerts']) !== false AND is_array($_SESSION['alerts']) AND count($_SESSION['alerts'])) {
            $alerts = array_merge($_SESSION['alerts'], $alerts);
        }
        /**
         *   types:
         *   - default
         *   - success
         *   - warning
         *   - error
         **/

        $alert = '<div class="kn-toast-alert">';
        if (count($alerts)) {

            foreach ($alerts as $k => $a) {
                switch ($a['status']) {
                    case 'error':
                        $a['status'] = 'danger';
                        break;
                    
                    case 'default':
                        $a['status'] = 'dark';
                        break;
                }
                $alert .= '<div class="kn-alert kn-alert-' . $a['status'] . '">' . $a['message'] . '</div>';

                if (isset($_SESSION['alerts'][$k]) !== false) 
                    unset($_SESSION['alerts'][$k]);
            }
        }
        $alert .= '</div>';

        return $alert;

    }


    /**
     * Language Translation
     * @param  $key 
     * @return $key translated string    
     */
    public static function lang($key) {

        global $languageFile;

        $key = strpos($key, '.') !== false ? explode('.', $key) : [$key];

        $terms = $languageFile;
        foreach ($key as $index) {
            if (isset($terms[$index]) !== false) {
                $terms = $terms[$index];
                $key = $terms;
            }
        }

        if (is_array($key)) {

            $key = $index;

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
     * @param bool $onlyToken  output option
     * @return string|null
     */
    public static function createCSRF($onlyToken = false) {


        $return = null;
        if (isset($_COOKIE[KN_SESSION_NAME]) !== false) {

            $csrf = [
                'cookie'        => self::authCode(),
                'timeout'       => strtotime('+1 hour'),
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

        $return = false;
        $token = @json_decode(self::decryptKey($token), true);
        if (is_array($token)) {

            if (
                (isset($token['cookie']) !== false AND $token['cookie'] == $_COOKIE[KN_SESSION_NAME]) AND
                (isset($token['timeout']) !== false AND $token['timeout'] >= time()) AND
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
        if (is_string($key) AND isset($_SESSION[$key]) !== false) {
            $return = $_SESSION[$key];
        } elseif (is_null($key)) {
            $return = $_SESSION;
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

            $_SESSION['user'] = $data;
        }

        return $_SESSION;

    }

    /**
     * Clear Session
     * Clear session data
     * @return void
     */
    public static function clearSession() {

        if (isset($_SESSION['user']) !== false) {
            unset($_SESSION['user']);
        }

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
        elseif ($bytes > 1) $bytes = $bytes . ' ' . self::lang('base.byte') . self::lang('lang.plural_suffix');
        elseif ($bytes == 1) $bytes = $bytes . ' ' . self::lang('base.byte');
        else $bytes = '0 ' . self::lang('base.byte');

        return $bytes;

    }

    /**
     * Remove directory
     * @param $folder
     * @return bool
     */
    public static function removeDir($folder) {

        $d = new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS);
        $r = new RecursiveIteratorIterator($d, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ( $r as $file ) {
            $file->isDir() ?  rmdir($file->getPathname()) : unlink($file->getPathname());
        }

    }


    /**
     * Get User Device Details
     * @param string|null $ua
     * @return array
     */

    public static function userAgentDetails($ua = null): array {

        $ua = is_null($ua) ? self::getHeader() : $ua;
        $browser = '';
        $platform = '';
        $bIcon = 'ti ti-circle-x';
        $pIcon = 'ti ti-circle-x';

        $browserList = [
            'Trident\/7.0'          => ['Internet Explorer 11','ti ti-browser'],
            'MSIE'                  => ['Internet Explorer','ti ti-browser'],
            'Edge'                  => ['Microsoft Edge','ti ti-brand-edge'],
            'Edg'                   => ['Microsoft Edge','ti ti-brand-edge'],
            'Internet Explorer'     => ['Internet Explorer','ti ti-browser'],
            'Beamrise'              => ['Beamrise','ti ti-planet'],
            'Opera'                 => ['Opera','ti ti-brand-opera'],
            'OPR'                   => ['Opera','ti ti-brand-opera'],
            'Vivaldi'               => ['Vivaldi','ti ti-planet'],
            'Shiira'                => ['Shiira','ti ti-planet'],
            'Chimera'               => ['Chimera','ti ti-planet'],
            'Phoenix'               => ['Phoenix','ti ti-planet'],
            'Firebird'              => ['Firebird','ti ti-planet'],
            'Camino'                => ['Camino','ti ti-planet'],
            'Netscape'              => ['Netscape','ti ti-planet'],
            'OmniWeb'               => ['OmniWeb','ti ti-planet'],
            'Konqueror'             => ['Konqueror','ti ti-planet'],
            'icab'                  => ['iCab','ti ti-planet'],
            'Lynx'                  => ['Lynx','ti ti-planet'],
            'Links'                 => ['Links','ti ti-planet'],
            'hotjava'               => ['HotJava','ti ti-planet'],
            'amaya'                 => ['Amaya','ti ti-planet'],
            'MiuiBrowser'           => ['MIUI Browser','ti ti-planet'],
            'IBrowse'               => ['IBrowse','ti ti-planet'],
            'iTunes'                => ['iTunes','ti ti-planet'],
            'Silk'                  => ['Silk','ti ti-planet'],
            'Dillo'                 => ['Dillo','ti ti-planet'],
            'Maxthon'               => ['Maxthon','ti ti-planet'],
            'Arora'                 => ['Arora','ti ti-planet'],
            'Galeon'                => ['Galeon','ti ti-planet'],
            'Iceape'                => ['Iceape','ti ti-planet'],
            'Iceweasel'             => ['Iceweasel','ti ti-planet'],
            'Midori'                => ['Midori','ti ti-planet'],
            'QupZilla'              => ['QupZilla','ti ti-planet'],
            'Namoroka'              => ['Namoroka','ti ti-planet'],
            'NetSurf'               => ['NetSurf','ti ti-planet'],
            'BOLT'                  => ['BOLT','ti ti-planet'],
            'EudoraWeb'             => ['EudoraWeb','ti ti-planet'],
            'shadowfox'             => ['ShadowFox','ti ti-planet'],
            'Swiftfox'              => ['Swiftfox','ti ti-planet'],
            'Uzbl'                  => ['Uzbl','ti ti-planet'],
            'UCBrowser'             => ['UCBrowser','ti ti-planet'],
            'Kindle'                => ['Kindle','ti ti-planet'],
            'wOSBrowser'            => ['wOSBrowser','ti ti-planet'],
            'Epiphany'              => ['Epiphany','ti ti-planet'],
            'SeaMonkey'             => ['SeaMonkey','ti ti-planet'],
            'Avant Browser'         => ['Avant Browser','ti ti-planet'],
            'Chrome'                => ['Google Chrome','ti ti-brand-chrome'],
            'CriOS'                 => ['Google Chrome','ti ti-brand-chrome'],
            'Safari'                => ['Safari','ti ti-brand-safari'],
            'Firefox'               => ['Firefox','ti ti-brand-firefox'],
            'Mozilla'               => ['Mozilla','ti ti-brand-firefox']
        ];

        $platformList = [
            'windows'               => ['Windows','ti ti-brand-windows'],
            'iPad'                  => ['iPad','ti ti-brand-apple'],
            'iPod'                  => ['iPod','ti ti-brand-apple'],
            'iPhone'                => ['iPhone','ti ti-brand-apple'],
            'mac'                   => ['Apple MacOS','ti ti-brand-apple'],
            'android'               => ['Android','ti ti-brand-android'],
            'linux'                 => ['Linux','ti ti-brand-open-source'],
            'Nokia'                 => ['Nokia','ti ti-brand-windows'],
            'BlackBerry'            => ['BlackBerry','ti ti-brand-open-source'],
            'FreeBSD'               => ['FreeBSD','ti ti-brand-open-source'],
            'OpenBSD'               => ['OpenBSD','ti ti-brand-open-source'],
            'NetBSD'                => ['NetBSD','ti ti-brand-open-source'],
            'UNIX'                  => ['UNIX','ti ti-brand-open-source'],
            'DragonFly'             => ['DragonFlyBSD','ti ti-brand-open-source'],
            'OpenSolaris'           => ['OpenSolaris','ti ti-brand-open-source'],
            'SunOS'                 => ['SunOS','ti ti-brand-open-source'],
            'OS\/2'                 => ['OS/2','ti ti-brand-open-source'],
            'BeOS'                  => ['BeOS','ti ti-brand-open-source'],
            'win'                   => ['Windows','ti ti-brand-windows'],
            'Dillo'                 => ['Linux','ti ti-brand-open-source'],
            'PalmOS'                => ['PalmOS','ti ti-brand-open-source'],
            'RebelMouse'            => ['RebelMouse','ti ti-brand-open-source']
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

        $str = str_replace(['\'', '"'], '', html_entity_decode($str));
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
                $data = \Transliterator::create("Any-Title")->transliterate($data);
                break;

            case 'uppercasefirst':
            case 'ucf':
                $data = \Transliterator::create("Any-Title")->transliterate($data);
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
                $data = \Transliterator::create("Any-Lower")->transliterate($data);
                break;

            case 'uppercase':
            case 'u':
                $data = \Transliterator::create("Any-Upper")->transliterate($data);
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
        $text = openssl_encrypt((string)$text, $ciphering, $encryptionKey, 0, $encryptionIv);
        return bin2hex($text);

    }


    /**
     * Data Decrypter
     * @param string $encryptedString
     * @return string
     */
    public static function decryptKey($encryptedString): string {

        $ciphering = "AES-128-CTR";
        $decryptionIv = '1234567891011121';
        $decryptionKey = md5(self::config('app.name'));
        return openssl_decrypt(hex2bin($encryptedString), $ciphering, $decryptionKey, 0, $decryptionIv);

    }

    /**
     * Write the value of the submitted field.
     * @param string $name
     * @param array|object $parameters
     * @param string $type  format parameter
     * @return string
     */
    public static function inputValue($name, $parameters, $type = '') {

        $return = '';

        $parameters = (array)$parameters;
        if (isset($parameters[$name]) !== false) {
            $return = $parameters[$name];

            if ($type == 'date' AND ! is_null($return)) {
                $return = date('Y-m-d', (int) $return);
            }

            $return = 'value="' . $return . '"';
        }
        return $return;

    }

    /**
     * Get auth status
     * @return bool
     */
    public static function isAuth() {

        return isset($_SESSION['user']->id) !== false ? true : false;

    }

    /**
     * Private data cleaner
     * @param array|object $data
     * @return array|object
     */
    public static function privateDataCleaner($data) {

        $return = is_object($data) ? (object)[] : [];
        foreach ($data as $k => $v) {

            if (is_array($v)) {
                $v = self::privateDataCleaner($v);
            } else {
                $v = in_array($k, ['password']) !== false ? '***' : $v;
            }

            if (is_object($return)) {
                $return->{$k} = $v;
            } else {
                $return[$k] = $v;
            }

        }
        return $return;
    }

    /**
     * Generate a Token
     * @param int $length
     * @return string
     */

    public static function tokenGenerator($length = 80): string {

        $key = '';
        list($usec, $sec) = explode(' ', microtime());
        $inputs = array_merge(range('z','a'), range(0,9), range('A','Z'));
        for ($i=0; $i<$length; $i++) {
            $key .= $inputs[mt_rand(0, (count($inputs)-1))];
        }
        return $key;

    }

    /**
     * Get Auth Code
     * @return string
     */

    public static function authCode() {

        return isset($_COOKIE[KN_SESSION_NAME]) !== false ? $_COOKIE[KN_SESSION_NAME] : null;

    }

    /**
     * Return a User Info from Session
     * @param string $key
     * @return string
     */
    public static function userData($key) {

        if ($key == 'auth_code')
            return $_COOKIE[KN_SESSION_NAME];

        $return = isset($_SESSION['user']->{$key}) !== false ? $_SESSION['user']->{$key} : null;
        if ($key == 'b_date' AND $return) {
            $return = date('Y-m-d', (int) $return);
        }
        return $return;

    }

    /**
     * Clean given HTML tags from a string
     * @param string $data  full html string
     * @param array $ tags  given tags
     * @return string
     */
    public static function cleanHTML($data, $tags = []) {

        $reg = [];
        foreach($tags as $tag) {

            if (in_array($tag, ['meta', 'hr', 'br']))
                $reg[] = '<'.$tag.'[^>]*>';

            else
                $reg[] = '<'.$tag.'[^>]*>.+?<\/'.$tag.'>';
            
        }

        $reg = implode('|', $reg);

        return preg_replace('/('.$reg.')/is', '', $data);

    }


    /**
     * UUID generator.
     * @return string
     */
    public static function generateUUID() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }


    /**
     * String shortener.
     * @param string text           long text
     * @param integer length        string length 
     * @param boolean withDots      export with 3 dots
     * @return string
     */
    public static function stringShortener($text, $length=20, $withDots=true) {

        if (strlen($text) > $length) {
            if ($withDots) {
                $withDots = '...';
                $length = $length - 3;
            } else $withDots = '';

            if (function_exists("mb_substr")) $text = trim(mb_substr($text, 0, $length, "UTF-8")).$withDots;
            else $text = trim(substr($text, 0, $length)).$withDots;
        }

        return $text;

    }
}