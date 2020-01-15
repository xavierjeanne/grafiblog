<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface
{

    /**
     * Undocumented variable
     *
     * @var string
     */
    private $query;


    /**
     * PaginatedQuery constructor
     *
     * @param string $query Request with x results
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * get count result
     *
     * @return int
     */
    public function getNbResults(): int
    {
        return $this->query->count();
    }
    /**
     * return slice of the results
     *
     * @param int $offset
     * @param int $length
     * @return QueryResult|traversable the slice
     */
    public function getSlice($offset, $length): QueryResult
    {
        $query = clone $this->query;
        return $query->limit($length, $offset)->fetchAll();
    }
}
