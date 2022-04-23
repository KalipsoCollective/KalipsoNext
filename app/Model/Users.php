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


    public function __construct () {

        parent::__construct();
        $this->created = true;
        $this->updated = true;
        $this->table('users');
    }

}