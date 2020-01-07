<?php

namespace App\Blog\Table;

use stdClass;

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
     * @return \stdClass[]
     */
    public function findPaginated()
    {
        return $this->pdo->query('SELECT * FROM posts ORDER BY created_at DESC LIMIT 10')->fetchAll();
    }

    /**
     * find article by id
     *
     * @param  int $id
     *
     * @return \stdClass
     */
    public function find(int $id): \stdClass
    {
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$id]);
        return $query->fetch();
    }
}
