<?php

use JoaoJoyce\Phlatdb\LineEncoder\JsonLineEncoder;
use JoaoJoyce\Phlatdb\Phlatdb;

class PhlatdbTest extends PHPUnit_Framework_TestCase {


    public function testAddingData()
    {
        $data = array(array('key1' => "Teste","key2" => "Teste"));
        $phlatdb = new Phlatdb(new JsonLineEncoder());
        $phlatdb->insert($data);

        $result =$phlatdb->getData();

        $this->assertEquals($data,$result);

    }

    public function testRemoveCorruptDataArray()
    {
        $data = array(array("asdf"),array('key1' => "Teste","key2" => "Teste"));
        $phlatdb = new Phlatdb(new JsonLineEncoder());
        $phlatdb->insert($data);

        $result =$phlatdb->getData();

        $this->assertNotEquals($data,$result);
        $this->assertEquals(array(array('key1' => "Teste","key2" => "Teste")),$result);
    }


}
 