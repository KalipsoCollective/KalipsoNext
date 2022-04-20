<?php

/**
 * @package KN
 * @subpackage Model
 */

declare(strict_types=1);

namespace KN\Model;

use KN\Model\Model;
use KN\Helpers\Base;

final class Log extends Model {


    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs';

    public function __construct () {

    	$this->table($this->table);
       
    }

    public function insert (array $data, $type = false) {

    	Base::dump($data);
       
    }

}