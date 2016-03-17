<?php  namespace JoaoJoyce\Phlatdb\Eloquent;

use Illuminate\Database\Query\Builder;
use JoaoJoyce\Phlatdb\Phlatdb;

class QueryBuilder extends Builder {

    private $phlatdb;

    public function __construct(Phlatdb $phlatdb)
    {
        $this->phlatdb = $phlatdb;
    }


    public function all($columns = ['*']) {


        return $this->phlatdb->all();

    }

}
 