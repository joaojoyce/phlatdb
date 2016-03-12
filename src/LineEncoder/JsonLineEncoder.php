<?php namespace JoaoJoyce\Phlatdb\LineEncoder;

class JsonLineEncoder implements LineEncoderInterface
{

    public function encodeToDB($data) {
        return json_encode($data);
    }

    public function decodeFromDB($string) {
        return json_decode($string);
    }


}