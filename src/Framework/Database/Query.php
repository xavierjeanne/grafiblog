<?php

namespace Framework\Database;

use IteratorAggregate;
use Pagerfanta\Pagerfanta;

class Query implements \IteratorAggregate
{
    private $select;
    private $from;
    private $where = [];
    private $order = [];
    private $limit;

    private $joins;
    private $pdo;
    private $params = [];
    private $entity;



    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }
    public function from(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->from[$table] = $alias;
        } else {
            $this->from[] = $table;
        }
        return $this;
    }
    /**
     * specification of select fields
     *
     * @param string ...$fields
     * @return Query
     */
    public function select(string ...$fields): self
    {
        $this->select = $fields;
        return $this;
    }
    public function limit(int $length, int $offset = 0): self
    {
        $this->limit = "$offset,$length";
        return $this;
    }
    public function order(string $order): self
    {
        $this->order[] = $order;
        return $this;
    }
    public function where(string ...$condition): self
    {
        $this->where = array_merge($this->where, $condition);
        return $this;
    }
    /**
     * add join to request sql
     *
     * @param string $table
     * @param string $condition
     * @param string $type
     * @return Query
     */
    public function join(string $table, string $condition, string $type = "left"): self
    {
        $this->joins[$type][] = [$table, $condition];
        return $this;
    }

    /**
     * return count of records
     *
     * @return integer
     */
    public function count(): int
    {
        //clone query in order to prevent mutation of query
        $query = clone $this;
        $table = current($this->from);
        return $query->select("COUNT($table.id)")->execute()->fetchColumn();
    }

    /**
     * add params to request
     *
     * @param array $params
     * @return Query
     */
    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * specification of the entity to use
     *
     * @param string $entity
     * @return Query
     */
    public function into(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * execute request
     *
     * @return QueryResult
     */
    public function fetchAll(): QueryResult
    {
        return new QueryResult($this->records = $this->execute()->fetchAll(\PDO::FETCH_ASSOC), $this->entity);
    }

    /**
     * catch a result
     *
     * @return void
     */
    public function fetch()
    {
        $record = $this->execute()->fetch(\PDO::FETCH_ASSOC);
        if ($record === false) {
            return false;
        }
        if ($this->entity) {
            return Hydrator::hydrate($record, $this->entity);
        }
        return $record;
    }

    /**
     * return a result or a norecordexception
     *
     * @return bool/mixed
     * @throws NoRecordException
     */
    public function fetchOrFail()
    {
        $record = $this->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

    /**
     * pagintae result
     *
     * @param integer $perPage
     * @param integer $currentPage
     * @return Pagerfanta
     */
    public function paginate(int $perPage, int $currentPage = 1): Pagerfanta
    {
        $paginator = new PaginatedQuery($this);
        return (new Pagerfanta($paginator))->setMaxPerPage($perPage)->setCurrentPage($currentPage);
    }
    /**
     * write request with all elements
     *
     * @return string
     */
    public function __toString()
    {
        $parts = ['SELECT'];
        if ($this->select) {
            $parts[] = join(',', $this->select);
        } else {
            $parts[] = '*';
        }
        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();
        if (!empty($this->joins)) {
            foreach ($this->joins as $type => $joins) {
                foreach ($joins as [$table, $condition]) {
                    $parts[] = strtoupper($type) . " JOIN $table ON $condition";
                }
            }
        }
        if (!empty($this->where)) {
            $parts[] = "WHERE";
            $parts[] = "(" . join(') AND (', $this->where) . ')';
        }
        if (!empty($this->order)) {
            $parts[] = 'ORDER BY';
            $parts[] = join(',', $this->order);
        }
        if ($this->limit) {
            $parts[] = 'LIMIT ' . $this->limit;
        }
        return join(' ', $parts);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    private function buildFrom(): string
    {
        $from = [];
        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = "$key as $value";
            } else {
                $from[] = $value;
            }
        }
        return join(',', $from);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    private function execute()
    {
        $query = $this->__toString();
        if (!empty($this->params)) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        }
        return $this->pdo->query($query);
    }

    public function getIterator()
    {
        return $this->fetchAll();
    }
}
