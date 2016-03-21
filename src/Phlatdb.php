<?php namespace JoaoJoyce\Phlatdb;

use JoaoJoyce\Phlatdb\LineEncoder\LineEncoderInterface;

class Phlatdb {

    protected $table;

    public function __construct(LineEncoderInterface $line_encoder,$index_column='id') {
        $this->line_encoder = $line_encoder;
        $this->path = realpath(dirname(__FILE__)) . "/../tests/db";
        $this->index_column = $index_column;
    }

    public function table($table) {
        $this->table = $table;
    }

}