<?php

use JoaoJoyce\Phlatdb\LineEncoder\JsonLineEncoder;

class ConfigurationCheckTest extends PHPUnit_Framework_TestCase {

    public function testEncoding()
    {
        $test = [
                'test1' => 'Linha1 test1',
                'test2' => 'Linha1 test2'
            ];

        $json_encoder = new JsonLineEncoder();
        $results = $json_encoder->encodeToDB($test);

        $this->assertEquals('{"test1":"Linha1 test1","test2":"Linha1 test2"}',$results);
    }

}
