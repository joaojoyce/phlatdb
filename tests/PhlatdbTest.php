<?php

use JoaoJoyce\Phlatdb\LineEncoder\JsonLineEncoder;
use JoaoJoyce\Phlatdb\Phlatdb;

class PhlatdbTest extends PHPUnit_Framework_TestCase {


    public function testAddingData()
    {
        $data = array(array('key1' => "Teste","key2" => "Teste"));
        $phlatdb = new Phlatdb(new JsonLineEncoder());
        $phlatdb->insert($data);

        $result =$phlatdb->getRawData();

        $this->assertEquals(array(array(1=>array('key1' => "Teste","key2" => "Teste"))),$result);

    }

    public function testRemoveCorruptDataArray()
    {
        $data = array(array("asdf"),array('key1' => "Teste","key2" => "Teste"));
        $phlatdb = new Phlatdb(new JsonLineEncoder());
        $phlatdb->insert($data);

        $result =$phlatdb->getRawData();

        $this->assertNotEquals($data,$result);
        $this->assertEquals(array(array(1=>array('key1' => "Teste","key2" => "Teste"))),$result);
    }

    public function testSave()
    {
        $data = [
            [
                'test1' => 'Linha1 test1',
                'test2' => 'Linha1 test2'
            ],
            [
                'test1' => 'Linha2 test1',
                'test2' => 'Linha2 test2'
            ]
        ];

        $phlatdb = new Phlatdb(new JsonLineEncoder());
        $phlatdb->table("testdb")->insert($data)->save();


    }

}
 