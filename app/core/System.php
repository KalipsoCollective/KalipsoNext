<?php

/**
 * @package KN
 * @subpackage KN System
 */

declare(strict_types=1);

namespace KN\Core;

use KN\Helpers\Base;
use KN\Core\Log;

class System {

    public $route = null;
    public $lang = 'en';

    public function __construct() {

        global $languageFile;

        // powered_by header - please don't remove! 
        Base::http('powered_by');

        define('KN_SESSION_NAME', Base::config('app.session'));

        // session and output buffer start
        Base::sessionStart();
        ob_start();

        // route file importing
        $this->route = require Base::path('app/resources/route.php');

        // langauge file importing
        $sessionLanguageParam = Base::getSession('language');
        if (
            ! is_null($sessionLanguageParam) AND 
            file_exists($path = Base::path('app/resources/localization/'.$sessionLanguageParam.'.php'))
        ) {

            $this->lang = $sessionLanguageParam;
            $languageFile = require $path;

        } elseif (file_exists($path = Base::path('app/resources/localization/'.$this->lang.'.php'))) {

            $languageFile = require $path;
            Base::setSession($this->lang, 'language');

        } else {

            throw new \Exception("Language file is not found!");

        }

    }

    public function go () {

        $this->route->run();
        
    }

}