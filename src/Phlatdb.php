<?php namespace JoaoJoyce\Phlatdb;

use JoaoJoyce\Phlatdb\LineEncoder\LineEncoderInterface;

class Phlatdb {

    private $path = "../tests/db";

    private $data;

    private $table;

    public function __construct(LineEncoderInterface $line_encoder) {
        $this->line_encoder = $line_encoder;
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function insert($data) {
        $this->data = $data;
        return $this;
    }

    public function save() {
        if(!$this->table) {
            throw new \Exception('Table not found');
        } elseif(!$this->data) {
            throw new \Exception('Data not found');
        } else {
            $file_out = $this->path . '/' . $this->table . '.db';

        }
    }

}