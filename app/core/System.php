<?php

/**
 * @package KN
 * @subpackage KN Core
 */

declare(strict_types=1);

namespace App\Core;

class System extends Route   {

	public function __construct() {



    }

    public static function start () {

        echo self::test();
    }

}