<?php namespace JoaoJoyce\Phlatdb;

use JoaoJoyce\Phlatdb\LineEncoder\LineEncoderInterface;

class Phlatdb {

    private $path;

    private $data = array();

    private $table;

    private $data_file_name;

    private $meta_file_name;

    private $select_fields=null;

    private $where = array();

    private $index_column;

    private $order_by = array();

    private $current_order;

    public function __construct(LineEncoderInterface $line_encoder,$index_column='id') {
        $this->line_encoder = $line_encoder;
        $this->path = realpath(dirname(__FILE__)) . "/../tests/db";
        $this->index_column = $index_column;
    }

    public function table($table) {
        $this->table = $table;
        $this->data_file_name = $this->path . '/' . $this->table . '.db';
        $this->meta_file_name = $this->path . '/' . $this->table . '.meta.db';
        return $this;
    }

    public function insert($data) {

        $lines_to_insert = array();

        if($this->isAssociativeArray($data)) {
            $data = array($data);
        }

        foreach($data as $line) {
            if($this->isAssociativeArray($line) && $this->isValidLine($line)) {
                array_push($lines_to_insert,$line);
            }
        }

        $this->data = $lines_to_insert;
        return $this;
    }

    public function save() {
        if(!$this->table) {
            throw new \Exception('Table not found');
        } elseif(count($this->data) == 0) {
            throw new \Exception('Data not found');
        } else {
            $data = $this->prepareDataAndAssignIndexes($this->data);
            $merged_data = $this->mergeData($data['data']);
            $encoded_merged_data = $this->line_encoder->encodeToDB($merged_data);
            $this->writeToFile($encoded_merged_data);
            $this->writeMetaDataToFile($data['keys']);
            return $data['keys'];
        }
    }

    public function getRawData()
    {
        return $this->data;
    }

    private function isAssociativeArray($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private function isValidLine($arr) {
        return true;
    }

    private function mergeData($data) {
        $data_from_file = $this->getDataFromFile();
        $new_data = $data_from_file + $data;
        return $new_data;
    }

    private function writeToFile($encoded_data) {
        $file = fopen($this->data_file_name, "w");
        fwrite($file, $encoded_data);
        fclose($file);
    }

    private function writeMetaDataToFile($inserted_indexes) {

        $last_index = $this->getLastIndexForTable();
        $last_index = max(array_merge($inserted_indexes,array($last_index)));
        $file = fopen($this->meta_file_name, "w");
        fwrite($file, $this->line_encoder->encodeToDB(array('last_index'=> $last_index)));
        fclose($file);
    }


    private function getLastIndexForTable() {
        $last_index=0;
        try {
            $file_data = file_get_contents($this->path . '/' . $this->table . '.meta.db');
            $data = $this->line_encoder->decodeFromDB($file_data);
            if($data && array_key_exists('last_index',$data)) {
                $last_index = $data['last_index'];
            }
        } catch(\Exception $e) {
        }
        return $last_index;
    }

    private function prepareDataAndAssignIndexes($new_data) {
        $indexes = array();
        $lines_to_insert = array();
        $last_index = $this->getLastIndexForTable();
        foreach($new_data as $line) {
            $last_index = $last_index + 1;
            array_push($indexes,$last_index);
            $line[$this->index_column] = $last_index;
            $lines_to_insert[$last_index] =  $line;
        }
        return array('data'=>$lines_to_insert,'keys'=> $indexes);
    }

    private function getDataFromFile() {
        $data_from_file = array();
        try {
            $file_data = file_get_contents($this->data_file_name);
            $data_from_file = $this->line_encoder->decodeFromDB($file_data);
        } catch(\Exception $e) {
        }
        return $data_from_file;
    }

    public function delete($id) {
        $data = $this->getDataFromFile();
        unset($data[$id]);
        $this->writeToFile($this->line_encoder->encodeToDB($data));
    }

    public function find($id) {
        $data = $this->getDataFromFile();


        if(!$this->select_fields) {
            return json_decode(json_encode($data[$id]), FALSE);
        } else {
            $res = array();
            foreach($this->select_fields as $field) {
                $res[$field] = $data[$id][$field];
            }
            return $res;
        }
    }

    public function update($id,$data_to_update) {
        $data = $this->getDataFromFile();
        foreach($data_to_update as $key => $line) {
            $data[$id][$key] = $line;
        }
        $this->writeToFile($this->line_encoder->encodeToDB($data));
        return $data[$id];

    }

    public function select() {
        $fields = func_get_args();
        $this->select_fields=$fields;
        return $this;
    }

    public function all() {
        $data = $this->getDataFromFile();
        return $data;

    }

    public function where($column, $operator = null, $value = null, $boolean = 'and') {
        array_push($this->where,array(
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean
        ));
        return $this;
    }


    public function get() {

        $results = $this->getLinesFromQuery();
        $results = $this->orderResults($results);
        return $results;

    }

    private function getLinesFromQuery() {

        $results = array();
        $data = $this->getDataFromFile();

        foreach($data as $line) {
            $results = $this->parseOperations($line,$results);
        }
        return $results;

    }

    private function parseOperations($line,$results) {

        $op_results = array();

        foreach($this->where as $key => $operation) {
            $op_results[$key] = false;
            if($operation['boolean'] == 'and') {
                if(array_key_exists($operation['column'],$line)) {
                    if($operation['operator'] == '=' && $line[$operation['column']] == $operation['value']) {
                        $op_results[$key] = true;
                    }
                    if($operation['operator'] == '>' && $line[$operation['column']] > $operation['value']) {
                        $op_results[$key] = true;
                    }
                    if($operation['operator'] == '<' && $line[$operation['column']] < $operation['value']) {
                        $op_results[$key] = true;
                    }
                    if($operation['operator'] == '>=' && $line[$operation['column']] >= $operation['value']) {
                        $op_results[$key] = true;
                    }
                    if($operation['operator'] == '<=' && $line[$operation['column']] <= $operation['value']) {
                        $op_results[$key] = true;
                    }
                    if(($operation['operator'] == '!=' || $operation['operator'] == '<>') && $line[$operation['column']] != $operation['value']) {
                        $op_results[$key] = true;
                    }
                }
            }
        }

        $insert = true;
        foreach($op_results as $res) {
            $insert = $insert && $res;
        }

        if($insert) {
            array_push($results,$line);
        }
        return $results;
    }

    public function count() {
        $results = $this->getLinesFromQuery();
        return count($results);

    }

    public function orderBy($order,$direction) {
        array_push($this->order_by,array('order' => $order,'direction' => $direction));
        return $this;
    }

    private function orderResults($results) {



        $asc = function($a, $b) {
            if ($a[$this->current_order] == $b[$this->current_order]) {
                return 0;
            }
            return ($a[$this->current_order] < $b[$this->current_order]) ? -1 : 1;
        };

        $desc = function($a, $b) {
            if ($a[$this->current_order] == $b[$this->current_order]) {
                return 0;
            }
            return ($a[$this->current_order] < $b[$this->current_order]) ? 1 : -1;
        };

        if(!empty($this->order_by)) {
            foreach($this->order_by as $order) {
                $this->current_order = $order['order'];
                if($order['direction'] == 'desc') {
                    uasort($results, $desc);
                } else {
                    uasort($results, $asc);
                }
            }
        }

        return $results;
    }

}