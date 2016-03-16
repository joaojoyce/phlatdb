<?php  namespace JoaoJoyce\Phlatdb\Eloquent;

use JoaoJoyce\Phlatdb\LineEncoder\JsonLineEncoder;
use JoaoJoyce\Phlatdb\Phlatdb;

abstract class Model extends \Illuminate\Database\Eloquent\Model {

    public function newEloquentBuilder($query)
    {
        $phlatdb = new Phlatdb(new JsonLineEncoder());
        return new Builder($query,$phlatdb);
    }


}
 