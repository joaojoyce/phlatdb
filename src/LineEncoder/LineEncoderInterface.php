<?php namespace JoaoJoyce\Phlatdb\LineEncoder;

interface LineEncoderInterface
{

    public function encodeToDB($data);

    public function decodeFromDB($string);


}