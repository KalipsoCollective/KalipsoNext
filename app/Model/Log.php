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


    const CREATED = true;
    const UPDATED = false;


    public function __construct () {

    	return $this->db->table('logs');
       
    }

    public function add(array $data, $type = false) {

        if (self::CREATED) {

            $data['created_at'] = time();
            $data['created_by'] = Base::userData('id') ?? 0;

        }
        // Base::dump($data);
        // Base::dump($this, true);
        return $this->db->insert($data);
       
    }

}