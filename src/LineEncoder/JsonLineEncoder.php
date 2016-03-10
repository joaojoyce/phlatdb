<?php namespace JoaoJoyce\Phlatdb\LineEncoder;

class JsonLineEncoder implements LineEncoderInterface
{

    public function encodeToDB($info) {
        return json_encode($info);
    }

}