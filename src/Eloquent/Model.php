<?php  namespace JoaoJoyce\Phlatdb\Eloquent;

use JoaoJoyce\Phlatdb\LineEncoder\JsonLineEncoder;
use JoaoJoyce\Phlatdb\Phlatdb;

abstract class Model extends \Illuminate\Database\Eloquent\Model {

    private static $query_builder;

    public function newEloquentBuilder($query)
    {
        $phlatdb = new Phlatdb(new JsonLineEncoder());
        return new Builder($query,$phlatdb);
    }

    protected function newBaseQueryBuilder()
    {
        $phlatdb = new Phlatdb(new JsonLineEncoder());
        $phlatdb->table($this->table);
        self::$query_builder = new QueryBuilder($phlatdb);
        return self::$query_builder;
    }

    public static function all($columns = ['*']) {


        $instance = new static;

        return $instance->newBaseQueryBuilder()->all($columns);


    }

}
 