<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;
use Framework\Database\Table;
use Framework\Database\PaginatedQuery;
use App\Blog\Entity\Post as EntityPost;
use Framework\Database\NoRecordException;

class PostTable extends Table
{


    protected $entity = POST::class;

    protected $table = 'posts';

    protected function paginationQuery()
    {
        return "SELECT posts.id,posts.name,categories.name as category_name FROM {$this->table} LEFT JOIN categories ON posts.category_id = categories.id ORDER BY created_at DESC";
    }

    public function findPaginatedPublic(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery($this->pdo, "SELECT posts.*,categories.name as category_name,categories.slug as category_slug FROM posts LEFT JOIN categories ON posts.category_id=categories.id ORDER BY posts.created_at DESC", "SELECT COUNT(id) FROM  {$this->table} ", $this->entity);
        return (new Pagerfanta($query))->setMaxPerPage($perPage)->setCurrentPage($currentPage);
    }

    public function findPaginatedPublicForCategory(int $perPage, int $currentPage, int $categoryId): Pagerfanta
    {
        $query = new PaginatedQuery($this->pdo, "SELECT posts.*,categories.name as category_name,categories.slug as category_slug FROM posts LEFT JOIN categories ON posts.category_id=categories.id WHERE posts.category_id =:category ORDER BY posts.created_at DESC ", "SELECT COUNT(id) FROM  {$this->table} WHERE category_id=:category", $this->entity, ['category' => $categoryId]);
        return (new Pagerfanta($query))->setMaxPerPage($perPage)->setCurrentPage($currentPage);
    }

    public function findWithCategory(int $id)
    {
        return $this->fetchOrFail("SELECT posts.*,categories.name as category_name,categories.slug as category_slug FROM posts LEFT JOIN categories ON posts.category_id=categories.id WHERE posts.id =?", [$id]);
    }
}
