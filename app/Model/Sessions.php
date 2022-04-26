<?php

/**
 * @package KN
 * @subpackage Model
 */

declare(strict_types=1);

namespace KN\Model;

use KN\Core\Model;
use KN\Helpers\Base;

final class Sessions extends Model {

    function __construct () {

        $this->table = 'sessions';
        $this->created = false;
        $this->updated = false;

        parent::__construct();

    }

}