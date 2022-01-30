<?php

/**
 * @package KN
 * @subpackage Model
 */

declare(strict_types=1);

namespace App\Model;

use App\Core\DB;

class User {

    public $table = 'users';
    public $base = null;
    public $row = null;

    public function __construct() {

        $this->base = (new DB());
        $this->base->table($this->table);

    }

    public function setter($data) {

        if (is_null($this->row)) $this->row = (object)[];

        foreach ($data as $columnName => $columnValue) {
            
            $this->row->{$columnName} = $columnValue;

        }

    }

    public function getUserWithUnameOrEmail($data) {

        $get = $this->base->select('id, u_name, f_name, l_name, email, password, role_id, b_date, status')
                    ->where('u_name', $data)
                    ->orWhere('email', $data)
                    ->get();

        if ($get) {

            $this->setter($get);

        }

        return $this->row;

    }

}