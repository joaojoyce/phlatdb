<?php  namespace JoaoJoyce\Phlatdb\Eloquent; 

use \Illuminate\Database\Query\Builder as QueryBuilder;
use JoaoJoyce\Phlatdb\Phlatdb;

class Builder extends \Illuminate\Database\Eloquent\Builder {

    public $phlatdb;

    //Pass $id to phlatdb;
    public $id;

    public function __construct(QueryBuilder $query,Phlatdb $phlatdb)
    {
        $this->phlatdb = $phlatdb;
        $this->query = $query;
    }

    public function find($id, $columns = ['*']) {
        return $this->phlatdb->find($id);
    }

    public function findMany($ids, $columns = ['*'])
    {
        if (empty($ids)) {
            return $this->model->newCollection();
        }

        $results = array();
        foreach($ids as $id) {
            array_push($results,$this->phlatdb->find($id));
        }

        return $results;
    }


    public function setModel(\Illuminate\Database\Eloquent\Model  $model)
    {
        $this->model = $model;
        $this->id = $this->model->getQualifiedKeyName();

        $this->phlatdb->table($model->getTable());
        $this->query->from($model->getTable());

        return $this;
    }


}
 