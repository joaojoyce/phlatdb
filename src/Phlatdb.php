<?php namespace JoaoJoyce\Phlatdb;

use JoaoJoyce\Phlatdb\LineEncoder\LineEncoderInterface;

class Phlatdb {

    private $path;

    private $data = array();

    private $table;

    public function __construct(LineEncoderInterface $line_encoder) {
        $this->line_encoder = $line_encoder;
        $this->path = realpath(dirname(__FILE__)) . "/../tests/db";
    }

    public function table($table) {
        $this->table = $table;
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
            $data = $this->prepareDataAndassignIndexes($this->data);
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
        $file_name = $this->path . '/' . $this->table . '.db';
        try {
            $file_data = file_get_contents($file_name);
            $data_from_file = json_decode($file_data);
            $new_data = array_merge($data_from_file, $data);
        } catch(\Exception $e) {
            $new_data = $data;
        }
        return $new_data;
    }

    private function writeToFile($encoded_data) {
        $file_name = $this->path . '/' . $this->table . '.db';
        $file = fopen($file_name, "w");
        fwrite($file, $encoded_data);
        fclose($file);
    }

    private function writeMetaDataToFile($inserted_indexes) {

        $last_index = $this->getLastIndexForTable();
        $last_index = max(array_merge($inserted_indexes,array($last_index)));
        $file_name = $this->path . '/' . $this->table . '.meta.db';
        $file = fopen($file_name, "w");
        fwrite($file, json_encode(array('last_index'=> $last_index)));
        fclose($file);
    }


    private function getLastIndexForTable() {
        $last_index=0;
        try {
            $file_data = file_get_contents($this->path . '/' . $this->table . '.meta.db');
            $data = json_decode($file_data);
            if($data && array_key_exists('last_index',$data)) {
                $last_index = $data->last_index;
            }
        } catch(\Exception $e) {
        }
        return $last_index;
    }

    private function prepareDataAndassignIndexes($new_data) {
        $indexes = array();
        $lines_to_insert = array();
        $last_index = $this->getLastIndexForTable();
        foreach($new_data as $line) {
            $last_index = $last_index + 1;
            $new_array = array($last_index => $line);
            array_push($indexes,$last_index);
            array_push($lines_to_insert,$new_array);
        }
        return array('data'=>$lines_to_insert,'keys'=> $indexes);
    }

}