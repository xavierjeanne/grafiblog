<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post as EntityPost;
use Pagerfanta\Pagerfanta;
use Framework\Database\PaginatedQuery;
use App\Blog\Entity\Post;

class PostTable
{
    /**
     * Undocumented variable
     *
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    /**
     * Pagination of article
     *
     * @var int perPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery($this->pdo, 'SELECT * FROM posts', 'SELECT COUNT(id) FROM posts', Post::class);
        return (new Pagerfanta($query))->setMaxPerPage($perPage)->setCurrentPage($currentPage);
    }

    /**
     * find article by id
     *
     * @param  int $id
     *
     * @return Post
     */
    public function find(int $id): Post
    {
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$id]);
        $query->setFetchMode(\PDO::FETCH_CLASS, Post::class);
        return $query->fetch();
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
        $statement = $this->pdo->prepare("UPDATE posts SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }

    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        var_dump($fields);
        $values = array_map(function ($field) {
            return ':' . $field;
        }, $fields);
        $statement = $this->pdo->prepare("INSERT INTO posts (" . join(',', $fields) . ") VALUES (" . join(',', $values) . ")");
        return $statement->execute($params);
    }
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
        return $statement->execute([$id]);
    }
    private function buildFieldQuery(array $params)
    {
        return join(',', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }
}
