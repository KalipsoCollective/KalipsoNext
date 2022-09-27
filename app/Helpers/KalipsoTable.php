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
     * Database connection instance. 
     * (PDO)
     */
    protected $db;

    /**
     * Table name or SQL query to be processed.
     */
    protected $from;

    /**
     * Output data
     */
    protected $data = [];


    /**
     * Response init 
     * @return this
     */
    public function __construct() {
        $this->data = [
            'current_page' => 1,
            'total_page' => 0,
            'record_count' => 0,
            'filtered_count' => 0,
            'records' => []
        ];
        return $this;
    }


    /**
     * @param instance $instance    pdo connection data 
     * @return this
     */
    public function db($instance) {

        $this->db = $instance;
        return $this;
    }

    /**
     * @param string $from          SQL or table name
     * 
     */
    public function from($from) {

        $this->from = $from;
        return $this;
    }


    /**
     * Create query and processing
     * useful arguments;
     * - primary: bool
     * - formatter: function
     * - exclude: bool -> exclude this column in where queries
     * @param array $args           column arguments 
     */
    public function process($args) {

        /**
         * Record length 
         */
        $perPage = (isset($_GET['per_page']) !== false ? (int) $_GET['per_page'] : 10);
        if ($perPage <= 0) $perPage = 10;

        /**
         * Page number
         */
        $page = (isset($_GET['page']) !== false ? (int) $_GET['page'] : 1);
        if ($page <= 0) $page = 1;

        /**
         * Order 
         */
        $order = (isset($_GET['order']) !== false ? strip_tags($_GET['order']) : 'id,desc');
        if (strpos($order, ',') !== false) $order = explode(',', $order, 2);
        else $order = ['id', 'desc'];

        $order[1] = strtolower($order[1]);

        if ($order[1] !== 'asc' AND $order[1] !== 'desc')
            $order[1] = 'desc';

        if (! strlen($order[0]))
            $order[0] = 'id';

        /**
         * Search
         */
        $search = (isset($_GET['search']) !== false ? @json_decode(urldecode($_GET['search']), true) : []);
        if (! is_array($search)) $search = [];

        if (count($search)) {

            foreach ($search as $col => $keyword) {
                $search[$col] = trim(strip_tags($keyword));

                if (! strlen($search[$col])) {
                    unset($search[$col]);
                }
            }
        }

        /**
         * Full search 
         */
        $fullSearch = (isset($_GET['full_search']) !== false ? trim(strip_tags($_GET['full_search'])) : null);
        if (is_string($fullSearch) AND ! strlen($fullSearch)) $fullSearch = null;


        $where = [];
        $whereString = '';
        if (! is_null($fullSearch)) {

            $columns = $args;
            foreach ($columns as $col => $details) {
                if (isset($details['exclude']) !== false AND $details['exclude'])
                    unset($columns[$col]);
            }

            foreach (array_keys($columns) as $column) {
                $where[] = $column . ' LIKE "%' . $fullSearch . '%"';
            }

            $whereString = '(' . implode(' OR ', $where) . ')';
        }
        $where = [];
        if (count($search)) {
            foreach ($search as $column => $keyword) {
                $where[] = $column . ' LIKE "%' . $keyword . '%"';
            }
            $whereString .= ($whereString !== '' ? ' AND ' : ' ') . '(' . implode(' OR ', $where) . ')';
        }

        if ($whereString !== '') {
            $whereString = ' WHERE ' . trim($whereString);
        }

        // Get total record
        $totalSql = 'SELECT COUNT(id) AS total FROM ' . $this->from . $whereString;
        $total = $this->db->query($totalSql);
        if ($total === false) {
            $err = $this->db->errorInfo();
            die('<pre>#' . $err[1] . ': ' . $err[2] . '</pre>');
        } else {
            // $total->setFetchMode(\PDO::FETCH_OBJ);
            $total = $total->fetch();
            $total = (int) $total->total;
            $this->data['record_count'] = $total;
        }

        if (strpos($order[0], 'id') !== false OR strpos($order[0], '_order') !== false) {
            $order[0] = 'CAST('.$order[0].' AS unsigned)';
        }

        // Get results
        $resultSql = 'SELECT * FROM ' . $this->from . 
            $whereString . ' ORDER BY ' .  $order[0] . ' ' . $order[1] . ' 
            LIMIT ' . $perPage . ' OFFSET ' . (($page > 0 ? $page : 1) - 1) * $perPage;
        $result = $this->db->query($resultSql);

        if ($result === false) {
            $err = $this->db->errorInfo();
            die('<pre>#' . $err[1] . ': ' . $err[2] . '</pre>');
        } else {
            // $total->setFetchMode(\PDO::FETCH_OBJ);
            $result = $result->fetchAll();
        }


        foreach ($result as $index => $data) {
            
            $record = (object)[];
            foreach ($args as $column => $details) {

                $record->{$column} = null;
                if (isset($details['formatter']) !== false) {
                    $record->{$column} = $details['formatter']($data);
                } elseif (isset($data->{$column}) !== false) {
                    $record->{$column} = $data->{$column};
                }
            }

            $this->data['records'][$index] = $record;
        }

        $this->data['filtered_count'] = count($this->data['records']);
        $this->data['current_page'] = $page;
        $this->data['total_page'] = ceil($total / $perPage);
        
        return $this;
    }


    /**
     * Output method; 
     * true: direct write with json header, 
     * false: return as array
     * @param boolean $direct  output method
     * @return array|void
     */
    public function output($direct = false) {

        if ($direct) {
            ob_clean();
            ob_start();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($this->data);
            exit;
        } else {
            return $this->data;
        }
    }

}