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

        echo KN_ROOT;
        
        // route file importing
        $this->route = require KN::path('app/resources/route.php');

        // langauge file importing
        if (
            isset($_SESSION['language']) !== false AND 
            file_exists($path = KN::path('app/resources/localization/'.$_SESSION['language'].'.php'))
        ) {

            $this->lang = $_SESSION['language'];
            $languageFile = require KN::path($path);

        } elseif (file_exists($path = KN::path('app/resources/localization/'.$this->lang.'.php'))) {

            $languageFile = require KN::path($path);

        } else {

            throw new \Exception("Language file is not found!");

        }

        echo KN_ROOT;

    }

    public function go () {

        $this->route->run();
        
    }

}