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
            $file_name = $this->path . '/' . $this->table . '.db';
            $file = fopen($file_name, "w");
            $encoded_data = $this->line_encoder->encodeToDB($this->data);
            fwrite($file, $encoded_data);
        }
    }

    public function getData()
    {
        return $this->data;
    }

    private function isAssociativeArray($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private function isValidLine($arr) {
        return true;
    }

}