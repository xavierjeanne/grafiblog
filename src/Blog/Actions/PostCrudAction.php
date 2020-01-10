<?php

namespace App\Blog\Actions;

use Framework\Router;
use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use App\Framework\Validator;
use App\Blog\Table\PostTable;
use Framework\Actions\CrudAction;
use Framework\Session\FlashService;
use Psr\Http\Message\RequestInterface;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostCrudAction extends CrudAction
{

    protected $viewPath = "@blog/admin/posts";
    protected $routePrefix = "blog.admin";
    private $categoryTable;

    public function __construct(RendererInterface $renderer, PostTable $table, Router $router, FlashService $flash, CategoryTable $categoryTable)
    {
        parent::__construct($renderer, $table, $router, $flash);
        $this->categoryTable = $categoryTable;
    }

    protected function formParams($params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        return $params;
    }

    protected function getParams(ServerRequestInterface $request): array
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id']);
        }, ARRAY_FILTER_USE_KEY);
        $params = array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return $params;
    }
    protected function getValidator(ServerRequestInterface $request)
    {
        return (parent::getValidator($request))->required('content', 'name', 'slug', 'created_at', 'category_id')->length('content', 10)->length('name', 2, 250)->length('slug', 2, 50)->slug('slug')->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())->dateTime('created_at');
    }
    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new \Datetime();
        return $post;
    }
}
