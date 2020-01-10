<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post as EntityPost;
use Pagerfanta\Pagerfanta;
use Framework\Database\PaginatedQuery;
use App\Blog\Entity\Post;
use Framework\Database\Table;

class PostTable extends Table
{
    /**
     * Undocumented variable
     *
     * @var \PDO
     */
    private $pdo;

    protected $entity = POST::class;

    protected $table = 'posts';

    protected function paginationQuery()
    {
        return "SELECT posts.id,posts.name,categories.name as category_name FROM {$this->table} LEFT JOIN categories ON posts.category_id = categories.id ORDER BY created_at DESC";
    }
}
