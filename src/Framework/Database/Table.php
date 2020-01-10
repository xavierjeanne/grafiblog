<?php

namespace Framework\Database;

use App\Blog\Entity\Post as EntityPost;
use Pagerfanta\Pagerfanta;
use Framework\Database\PaginatedQuery;
use App\Blog\Entity\Post;

class Table
{
    /**
     *
     *
     * @var \PDO
     */
    private $pdo;
    /**
     * name of table in bdd
     *
     * @var string
     */
    protected $table;
    /**
     * entity to use
     *
     * @var string|null
     */
    protected $entity;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    /**
     * Pagination of
     *
     * @var int perPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery($this->pdo, $this->paginationQuery(), "SELECT COUNT(id) FROM  {$this->table} ", $this->entity);
        return (new Pagerfanta($query))->setMaxPerPage($perPage)->setCurrentPage($currentPage);
    }

    protected function paginationQuery()
    {
        return "SELECT * FROM  {$this->table}";
    }
    /**
     * get key value list of records
     *
     * @return array
     */
    public function findList(): array
    {
        $results = $this->pdo->query("SELECT id,name FROM {$this->table}")->fetchAll(\PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }
    /**
     * find element by id
     *
     * @param  int $id
     *
     * @return mixed
     */
    public function find(int $id)
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $query->execute([$id]);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetch() ?: null;
    }
    /**
     * update a post
     *
     * @param integer $id
     * @param array $params
     * @return boolean
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params["id"] = $id;
        $statement = $this->pdo->prepare("UPDATE $this->table SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }

    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(', ', $fields);
        $statement = $this->pdo->prepare(
            "INSERT INTO {$this->table} ($fields) VALUES ($values)"
        );
        return $statement->execute($params);
    }
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $statement->execute([$id]);
    }
    private function buildFieldQuery(array $params)
    {
        return join(',', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    public function getEntity(): string
    {
        return $this->entity;
    }
    public function getTable(): string
    {
        return $this->table;
    }
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
    public function exists($id): bool
    {
        $statement = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }
}
