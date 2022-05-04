<?php

/**
 * KalipsoTable Server-side Processing Class
 * @author KalipsoCollective
 * @package KN
 * @subpackage KN Helper
 */

declare(strict_types=1);

namespace KN\Helpers;

class KalipsoTable {

    /**
     * Database connection instance. (PDO)
     */
    protected $db;

    /**
     * Table name to be processed.
     */
    protected $table;

    /**
     * Output method; true: direct write with json header, false: return as array
     */
    protected $output = false;



    public function __construct() {
        return $this;
    }

    public function db($instance) {

        $this->db = $instance;
        return $this;
    }

    public function table($name) {

        $this->table = $name;
        return $this;
    }

    public function process($func) {

        
        return $this;
    }

    public function output(boolean $direct = null) {

        $this->output = $direct;
        return $this;
    }


    public function __destruct() {

        echo 'Finally!';

    }

}