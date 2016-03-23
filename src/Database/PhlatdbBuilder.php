<?php  namespace JoaoJoyce\Phlatdb\Database; 

use Illuminate\Database\Query\Builder;
use JoaoJoyce\Phlatdb\LineEncoder\JsonLineEncoder;
use JoaoJoyce\Phlatdb\Phlatdb;

class PhlatdbBuilder extends Builder {


    protected $phlatdb;


    public function __construct(Phlatdb $phlatdb) {
        $this->phlatdb = $phlatdb;
    }


    public function selectSub($query, $as)
    {
        new \Exception("selectSub not supported");
    }


    public function exists()
    {
        new \Exception("exists not supported");
    }

    /**
     * Insert a new record into the database.
     *
     * @param  array  $values
     * @return bool
     */
    public function insert(array $values)
    {
        if (empty($values)) {
            return true;
        }

        $phlatdb = new Phlatdb(new JsonLineEncoder());

        return !empty($phlatdb->table($this->from)->insert($values)->save());

    }


    /**
     * Insert a new record and get the value of the primary key.
     *
     * @param  array   $values
     * @param  string  $sequence
     * @return int
     */
    public function insertGetId(array $values, $sequence = null)
    {
        if (empty($values)) {
            return true;
        }

        $phlatdb = new Phlatdb(new JsonLineEncoder());

        $keys = $phlatdb->table($this->from)->insert($values)->save();

        if(!empty($keys)) {
            return $keys[0];
        }
        return null;

    }

    public function update(array $values)
    {
        $bindings = array_values(array_merge($values, $this->getBindings()));

        $sql = $this->grammar->compileUpdate($this, $values);

        return $this->connection->update($sql, $this->cleanBindings($bindings));
    }

    public function increment($column, $amount = 1, array $extra = [])
    {
        $wrapped = $this->grammar->wrap($column);

        $columns = array_merge([$column => $this->raw("$wrapped + $amount")], $extra);

        return $this->update($columns);
    }

    public function decrement($column, $amount = 1, array $extra = [])
    {
        $wrapped = $this->grammar->wrap($column);

        $columns = array_merge([$column => $this->raw("$wrapped - $amount")], $extra);

        return $this->update($columns);
    }

    public function delete($id = null)
    {
        // If an ID is passed to the method, we will set the where clause to check
        // the ID to allow developers to simply and quickly remove a single row
        // from their database without manually specifying the where clauses.
        if (! is_null($id)) {
            $this->where('id', '=', $id);
        }

        $sql = $this->grammar->compileDelete($this);

        return $this->connection->delete($sql, $this->getBindings());
    }

    public function truncate()
    {
        foreach ($this->grammar->compileTruncate($this) as $sql => $bindings) {
            $this->connection->statement($sql, $bindings);
        }
    }

    public function newQuery()
    {
        return new static($this->connection, $this->grammar, $this->processor);
    }

    public function raw($value)
    {
        return $this->connection->raw($value);
    }
}
 