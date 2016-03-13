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

        $this->assertEquals(array(array('key1' => "Teste","key2" => "Teste")),$result);

    }

    public function testRemoveCorruptDataArray()
    {
        $data = array(array("asdf"),array('key1' => "Teste","key2" => "Teste"));
        $phlatdb = new Phlatdb(new JsonLineEncoder());
        $phlatdb->insert($data);

        $result =$phlatdb->getRawData();

        $this->assertNotEquals($data,$result);
        $this->assertEquals(array(array('key1' => "Teste","key2" => "Teste")),$result);
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

    public function testInsertAndDeleteOneLine() {
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
        $inserted_ids = $phlatdb->table("testdb")->insert($data)->save();
        $phlatdb->delete($inserted_ids[0]);

    }

    public function testFindValue() {
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
        $inserted_ids = $phlatdb->table("testdb")->insert($data)->save();
        $res = $phlatdb->find($inserted_ids[1]);
        $this->assertEquals(['test1' => 'Linha2 test1','test2' => 'Linha2 test2'],$res);

    }

    public function testUpdateValue() {
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
        $inserted_ids = $phlatdb->table("testdb")->insert($data)->save();
        $res = $phlatdb->update($inserted_ids[1],['test1' => 'Linha3 test1']);
        $this->assertEquals(['test1' => 'Linha3 test1','test2' => 'Linha2 test2'],$res);

    }

    public function testSelectValues() {
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
        $inserted_ids = $phlatdb->table("testdb")->insert($data)->save();
        $res = $phlatdb->select('test1','test2')->find($inserted_ids[0]);
        $this->assertEquals(array('test1' => 'Linha1 test1' ,'test2' => 'Linha1 test2'),$res);

    }


}
 