<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface
{
    /**
     * Undocumented variable
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * Undocumented variable
     *
     * @var string
     */
    private $query;

    /**
     * Undocumented variable
     *
     * @var string
     */
    private $countQuery;

    /**
     * Undocumented variable
     *
     * @var string|null
     */
    private $entity;
    private $params;
    /**
     * PaginatedQuery constructor
     *
     * @param \PDO $pdo
     * @param string $query Request with x results
     * @param string $countQuery count result
     * @param string|null $entity
     */
    public function __construct(\PDO $pdo, string $query, string $countQuery, ?string $entity, array $params = [])
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entity = $entity;
        $this->params = $params;
    }

    /**
     * get count result
     *
     * @return int
     */
    public function getNbResults(): int
    {
        if (!empty($this->params)) {
            $query = $this->pdo->prepare($this->countQuery);
            $query->execute($this->params);
            return $query->fetchColumn();
        }
        return $this->pdo->query($this->countQuery)->fetchColumn();
    }
    /**
     * return slice of the results
     *
     * @param int $offset
     * @param int $length
     * @return array
     */
    public function getSlice($offset, $length): array
    {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset,:length');
        foreach ($this->params as $key => $param) {
            $statement->bindParam($key, $param);
        }
        $statement->bindParam('offset', $offset, \PDO::PARAM_INT);
        $statement->bindParam('length', $length, \PDO::PARAM_INT);
        if ($this->entity) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        $statement->execute();
        return $statement->fetchAll();
    }
}
