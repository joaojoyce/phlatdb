<?php  namespace JoaoJoyce\Phlatdb\Eloquent; 

use \Illuminate\Database\Query\Builder as QueryBuilder;
use JoaoJoyce\Phlatdb\Phlatdb;

class Builder extends \Illuminate\Database\Eloquent\Builder {

    public $phlatdb;

    public function __construct(QueryBuilder $query,Phlatdb $phlatdb)
    {
        $this->phlatdb = $phlatdb;
        $this->query = $query;
    }

    public function find($id, $columns = ['*']) {
        $this->phlatdb->find($id);
    }

    public function setModel(\Illuminate\Database\Eloquent\Model  $model)
    {
        $this->model = $model;

        $this->phlatdb->table($model->getTable());
        $this->query->from($model->getTable());

        return $this;
    }


}
 