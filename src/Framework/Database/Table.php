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
    protected $pdo;
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

    public function findAll(): array
    {
        $statement = $this->pdo->query("SELECT * FROM {$this->table}");
        if ($this->entity) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(\PDO::FETCH_OBJ);
        }
        return $statement->fetchAll();
    }
    /**
     * get a line by a field
     *
     * @param string $field
     * @param string $value
     * @return array
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE {$field} = ?", [$value]);
    }
    /**
     * find element by id
     *
     * @param  int $id
     *
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }
    /**
     * get count of register
     *
     * @return integer
     */
    public function count(): int
    {
        return $this->fetchColumn("SELECT COUNT(id) FROM {$this->table}");
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
    /**
     * allow to make a request and get the first result
     *
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws NoRecordException
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        $record = $query->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }
    /**
     * get first column
     *
     * @param string $query
     * @param array $params
     * @return void
     */
    private function fetchColumn(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetchColumn();
    }
}
