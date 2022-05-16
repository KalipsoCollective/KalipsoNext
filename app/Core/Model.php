<?php

/**
 * @package KN
 * @subpackage KN Model
 */

declare(strict_types=1);

namespace KN\Core;

use KN\Helpers\Base;
use \Buki\Pdox;
use \PDO;

class Model extends Pdox {

    protected $table = '';
    protected $created = false;
    protected $updated = false;

    public function __construct() {

        parent::__construct([
            'host'      => Base::config('database.host'),
            'driver'    => Base::config('database.driver'),
            'database'  => Base::config('database.name'),
            'username'  => Base::config('database.user'),
            'password'  => Base::config('database.pass'),
            'charset'   => Base::config('database.charset'),
            'collation' => Base::config('database.collation'),
            'prefix'    => Base::config('database.prefix'),
        ]);

        $this->table($this->table);
    }

    public function insert(array $data, $type = false) {

        if ($this->created) {

            $data['created_at'] = isset($data['created_at']) === false ? time() : $data['created_at'];
            $data['created_by'] = isset($data['created_at']) === false ? (Base::userData('id') ?? 0) : $data['created_at'];

        }

        return parent::insert($data, $type);

    }

    public function update(array $data, $type = false) {

        if ($this->updated) {

            $data['updated_at'] = isset($data['updated_at']) === false ? time() : $data['updated_at'];
            $data['updated_by'] = isset($data['updated_by']) === false ? (Base::userData('id') ?? 0) : $data['updated_by'];

        }

        return parent::update($data, $type);

    }

    protected function reset() {

        parent::reset();
        $this->table($this->table);

    }

    public function dbInit($schema) {

        // delete other tables
        $sql = "SELECT CONCAT(`TABLE_NAME`) FROM information_schema.TABLES WHERE TABLE_SCHEMA = \"" . Base::config('database.name') . "\"";
        $allTables = $this->pdo->prepare($sql);
        $allTables->execute();
        $allTables = $allTables->fetchAll(PDO::FETCH_COLUMN);
        
        if (is_array($allTables) AND count($allTables)) {

            foreach ($allTables as $table) {
                $this->pdo->exec("DROP TABLE IF EXISTS `" . $table . "`;");
            }

        }


        $sql = '';
        foreach ($schema['tables'] as $table => $columns) {

            // if ($table != 'relations') continue; // temporary

            $sql .= PHP_EOL . 'DROP TABLE IF EXISTS `' . $table . '`;' . PHP_EOL;
            $sql .= 'CREATE TABLE `' . $table . '` (';

            $externalParams = [];

            foreach ($columns['cols'] as $column => $attributes) {

                $type = '';

                switch ($attributes['type']) {
                    case 'int':
                        if (isset($attributes['type_values']) === false) $attributes['type_values'] = 11;
                        $type = 'int(' . $attributes['type_values'] . ')';
                        break;

                    case 'float':
                    case 'decimal':
                        if (isset($attributes['type_values']) !== false) $attributes['type_values'] = '('.$attributes['type_values'].')';
                        $type = $attributes['type'] . $attributes['type_values'];
                        break;

                    case 'varchar':
                        $type = 'varchar(' . $attributes['type_values'] . ') COLLATE ' .
                            (isset($attributes['collate']) !== false ? $attributes['collate'] :
                                $schema['table_values']['collate']);

                        break;

                    case 'json':
                        $type = 'JSON ';

                        break;

                    case 'text':
                        $type = 'text' . ' COLLATE ' .
                            (isset($attributes['collate']) !== false ? $attributes['collate'] :
                                $schema['table_values']['collate']);
                        break;

                    case 'longtext':
                        $type = 'longtext' . ' COLLATE ' .
                            (isset($attributes['collate']) !== false ? $attributes['collate'] :
                                $schema['table_values']['collate']);
                        break;

                    case 'enum':
                        $type = "enum('" . implode("', '", $attributes['type_values']) . "')";
                        break;

                }

                if (isset($attributes['index']) !== false) {

                    switch ($attributes['index']) {
                        case 'PRIMARY':
                            $externalParams[] = 'PRIMARY KEY (`' . $column . '`)';
                            break;

                        case 'INDEX':
                            $externalParams[] = 'INDEX `' . $column . '` (`' . $column . '`)';
                            break;

                        case 'UNIQUE':
                            $externalParams[] = 'UNIQUE(`' . $column . '`)';
                            break;

                        case 'FULLTEXT':
                            $externalParams[] = 'FULLTEXT(`' . $column . '`)';
                            break;
                    }

                }

                if (isset($attributes['attr']) !== false) {

                    $type .= ' ' . $attributes['attr'];

                }

                if (isset($attributes['nullable']) === false OR ! $attributes['nullable']) {
                    $type .= ' NOT NULL';
                }

                if (isset($attributes['default']) !== false) {
                    switch ($attributes['default']) {
                        case 'NULL':
                        case 'CURRENT_TIMESTAMP':
                            $type .= ' DEFAULT ' . $attributes['default'];
                            break;
                        default:
                            $type .= ' DEFAULT \'' . $attributes['default'] . '\'';
                            break;
                    }
                }

                if (isset($attributes['auto_inc']) !== false AND $attributes['auto_inc']) {
                    $type .= ' AUTO_INCREMENT';
                }

                $sql .= PHP_EOL . '   `' . $column . '` '. $type .',';

            }

            if (count($externalParams)) {

                foreach ($externalParams as $param) {
                    $sql .= PHP_EOL . '   ' . $param . ',';
                }

            }

            $sql = rtrim($sql, ',' . PHP_EOL);

            $engine = isset($schema['table_values']['specific'][$table]['engine'])  !== false ?
                $schema['table_values']['specific'][$table]['engine']
                : $schema['table_values']['engine'];

            $charset = isset($schema['table_values']['specific'][$table]['charset'])  !== false ?
                $schema['table_values']['specific'][$table]['charset']
                : $schema['table_values']['charset'];

            $collate = isset($schema['table_values']['specific'][$table]['collate'])  !== false ?
                $schema['table_values']['specific'][$table]['collate']
                : $schema['table_values']['collate'];


            $sql .= PHP_EOL . ') ENGINE=' . $engine .
                ' DEFAULT CHARSET=' . $charset .
                ' COLLATE=' . $collate . ';' . PHP_EOL;

        }

        try {

            // \KN\Helpers\Base::dump($sql);
            return $this->pdo->exec($sql);

        } catch(PDOException $e) {

            throw new Exception('DB Init action is not completed. ' . $e->getMessage());

        }

    }

    public function dbSeed($schema) {

        $sql = '';


        foreach ($schema['data'] as $table => $data) {


            $values = '';
            $sql .= PHP_EOL . 'TRUNCATE `' . $table . '`;' . PHP_EOL . 'INSERT INTO `' . $table . '` (';

            $i = 0;
            foreach ($data as $row) {

                $values .= '(';
                $item = [];
                $i++;

                foreach ($row as $column => $value) {

                    if ($i == 1) $sql .= '`' . $column . '`, ';

                    if ($value === 'NULL') {
                        $value = 'NULL';
                    } elseif (is_numeric($value)) {
                        $value = '' . $value . '';
                    } else {
                        if (is_string($value)) $value = addslashes($value);
                        $value = '"' . $value . '"';
                    }

                    $item[] = $value;

                }

                $values .= implode(', ', $item) . '),' . PHP_EOL;

            }

            $sql = rtrim($sql, ', ' . PHP_EOL) . ') VALUES ' . PHP_EOL .
                rtrim($values, ',' . PHP_EOL) . '; ' . PHP_EOL;

        }

        try {

            return $this->pdo->exec($sql);

        } catch(PDOException $e) {
            throw new Exception('DB Seed action is not completed. ' . $e->getMessage());

        }

    }

}