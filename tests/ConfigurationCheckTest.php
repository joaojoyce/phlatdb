<?php

use JoaoJoyce\Phlatdb\Phlatdb;

class ConfigurationCheckTest extends PHPUnit_Framework_TestCase {

    public function testFirstTest()
    {
        $phlat = new Phlatdb();
        $this->assertTrue($phlat->firstFunction());
    }

}
