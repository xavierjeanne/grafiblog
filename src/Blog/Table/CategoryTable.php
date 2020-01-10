<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post as EntityPost;
use Pagerfanta\Pagerfanta;
use Framework\Database\PaginatedQuery;
use App\Blog\Entity\Post;
use Framework\Database\Table;

class CategoryTable extends Table
{

    protected $table = 'categories';
}
