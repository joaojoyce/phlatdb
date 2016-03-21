<?php  namespace JoaoJoyce\Phlatdb\Database; 

use Closure;
use Illuminate\Database\ConnectionInterface;
use JoaoJoyce\Phlatdb\LineEncoder\JsonLineEncoder;
use JoaoJoyce\Phlatdb\Phlatdb;

class PhlatdbConnection implements ConnectionInterface {


    /**
     * @return Phlatdb
     */
    public function query() {
        return new Phlatdb(new JsonLineEncoder());
    }

    /**
     * Begin a fluent query against a database table.
     *
     * @param  string $table
     * @return \Illuminate\Database\Query\Builder
     */
    public function table($table)
    {
        $this->query()->table($table);
    }

    public function raw($value)
    {
        throw new \Exception("Raw queries not supported by Phlatdb");
    }

    /**
     * Run a select statement and return a single result.
     *
     * @param  string $query
     * @param  array $bindings
     * @return mixed
     */
    public function selectOne($query, $bindings = [])
    {
        $records = $this->select($query, $bindings);

        return count($records) > 0 ? reset($records) : null;
    }

    /**
     * Run a select statement against the database.
     *
     * @param  string $query
     * @param  array $bindings
     * @return array
     */
    public function select($query, $bindings = [])
    {
        // Query the database file....
    }

    /**
     * Run an insert statement against the database.
     *
     * @param  string $query
     * @param  array $bindings
     * @return bool
     */
    public function insert($query, $bindings = [])
    {
        return $this->statement($query, $bindings);
    }

    /**
     * Run an update statement against the database.
     *
     * @param  string $query
     * @param  array $bindings
     * @return int
     */
    public function update($query, $bindings = [])
    {
        return $this->affectingStatement($query,$bindings);
    }

    /**
     * Run a delete statement against the database.
     *
     * @param  string $query
     * @param  array $bindings
     * @return int
     */
    public function delete($query, $bindings = [])
    {
        return $this->affectingStatement($query,$bindings);
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param  string $query
     * @param  array $bindings
     * @return bool
     */
    public function statement($query, $bindings = [])
    {
        // TODO: Implement statement() method.
    }

    /**
     * Run an SQL statement and get the number of rows affected.
     *
     * @param  string $query
     * @param  array $bindings
     * @return int
     */
    public function affectingStatement($query, $bindings = [])
    {
        // TODO: Implement affectingStatement() method.
    }

    public function unprepared($query)
    {
        throw new \Exception("Raw queries not supported");
    }

    public function prepareBindings(array $bindings)
    {
        throw new \Exception("Not supported");
    }

    public function transaction(Closure $callback)
    {
        throw new \Exception("Transaction not supported");
    }

    public function beginTransaction()
    {
        throw new \Exception("Transaction not supported");
    }

    public function commit()
    {
        throw new \Exception("Transaction not supported");
    }

    public function rollBack()
    {
        throw new \Exception("Transaction not supported");
    }

    public function transactionLevel()
    {
        throw new \Exception("Transaction not supported");
    }

    public function pretend(Closure $callback)
    {
        throw new \Exception("Pretend not supported");
    }
}
 