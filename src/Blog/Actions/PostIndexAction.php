<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use PDO;
use Framework\Router;
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostIndexAction
{
    private $renderer;
    private $postTable;
    private $categoryTable;
    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, PostTable $postTable, CategoryTable $categoryTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->categoryTable = $categoryTable;
    }
    public function __invoke(Request $request)
    {
        $params = $request->getQueryParams();
        $posts = $this->postTable->findPublic()->paginate(12, $params['p'] ?? 1);
        $categories = $this->categoryTable->findAll();
        return $this->renderer->render('@blog/index', compact('posts', 'categories'));
    }
}
