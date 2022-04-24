<?php

/**
 * @package KN
 * @subpackage Model
 */

declare(strict_types=1);

namespace KN\Model;

use KN\Model\Model;
use KN\Helpers\Base;

final class Users extends Model {

    function __construct () {

        $this->table = 'users';
        $this->created = true;
        $this->updated = true;

        parent::__construct();

    }

}