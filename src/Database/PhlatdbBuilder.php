<?php  namespace JoaoJoyce\Phlatdb\Database; 

use Illuminate\Database\Query\Builder;
use JoaoJoyce\Phlatdb\Phlatdb;

class PhlatdbBuilder extends Builder {


    protected $phlatdb;


    public function __construct(Phlatdb $phlatdb) {
        $this->phlatdb = $phlatdb;
    }




}
 