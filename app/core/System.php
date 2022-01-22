<?php

/**
 * @package KN
 * @subpackage KN System
 */

declare(strict_types=1);

namespace App\Core;

use App\Helpers\KN;

class System {

    public $route = null;
    public $lang = 'en';

    public function __construct() {

        global $languageFile;

        // powered_by header - please don't remove!
        KN::http('powered_by');

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

            $this->lang = $_SESSION['language'];
            $languageFile = require $path;

        } elseif (file_exists($path = KN::path('app/resources/localization/'.$this->lang.'.php'))) {

            $languageFile = require $path;
            KN::setSession('language', $this->lang);

        } else {

            throw new \Exception("Language file is not found!");

        }

    }

    public function go () {

        $this->route->run();
        
    }

}