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
            $merged_data = $this->mergeData();
            $new_data = $this->line_encoder->encodeToDB($merged_data);
            $this->writeToFile($new_data);
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

    private function mergeData() {
        $file_name = $this->path . '/' . $this->table . '.db';
        $file_data = file_get_contents($file_name);
        $data = json_decode($file_data);
        $new_data = array_merge($data,$this->data);
        return $new_data;
    }

    private function writeToFile($encoded_data) {
        $file_name = $this->path . '/' . $this->table . '.db';
        $file = fopen($file_name, "w");
        fwrite($file, $encoded_data);
        fclose($file);
    }

}