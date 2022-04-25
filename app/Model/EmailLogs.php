<?php

/**
 * @package KN
 * @subpackage Model
 */

declare(strict_types=1);

namespace KN\Model;

use KN\Core\Model;
use KN\Helpers\Base;

final class EmailLogs extends Model {

    function __construct () {

        $this->table = 'email_logs';
        $this->created = false;
        $this->updated = false;

        parent::__construct();

    }
}