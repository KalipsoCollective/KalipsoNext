<?php

/**
 * @package KN
 * @subpackage KN System
 */

declare(strict_types=1);

namespace KN\Core;

use KN\Helpers;

class System {

    public $route = null;
    public $lang = 'en';

    public function __construct() {

        global $languageFile;

        // powered_by header - please don't remove!
        KN::http('powered_by');

        define('KN_SESSION_NAME', KN::config('app.session'));

        // session and output buffer start
        KN::sessionStart();
        ob_start();

        // route file importing
        $this->route = require KN::path('app/resources/route.php');

        // langauge file importing
        $sessionLanguageParam = KN::getSession('language');
        if (
            ! is_null($sessionLanguageParam) AND 
            file_exists($path = KN::path('app/resources/localization/'.$sessionLanguageParam.'.php'))
        ) {

            $this->lang = $sessionLanguageParam;
            $languageFile = require $path;

        } elseif (file_exists($path = KN::path('app/resources/localization/'.$this->lang.'.php'))) {

            $languageFile = require $path;
            KN::setSession($this->lang, 'language');

        } else {

            throw new \Exception("Language file is not found!");

        }

    }

    public function go () {

        $this->route->run();
        
    }

}