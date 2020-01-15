<?php

namespace Framework\Database;

use App\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;
use Framework\Database\Query;
use Framework\Database\PaginatedQuery;
use App\Blog\Entity\Post as EntityPost;

class Table
{
    /**
     *
     *
     * @var null|\PDO
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
     * @var string
     */
    protected $entity = \stdClass::class;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
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
     * return a query on a table with the name of table and the first letter of table as alias
     *
     * @return Query
     */
    public function makeQuery(): Query
    {
        return (new Query($this->pdo))->from($this->table, $this->table[0])->into($this->entity);
    }

    /**
     * return a query result for all records on a table
     *
     * @return Query
     */
    public function findAll(): Query
    {
        return $this->makeQuery();
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
        return $this->makequery()->where("$field = :field")->params(["field" => $value])->fetchOrFail();
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
        return $this->makequery()->where("id = $id")->fetchOrFail();
    }
    /**
     * get count of register
     *
     * @return integer
     */
    public function count(): int
    {
        return $this->makeQuery()->count();
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
