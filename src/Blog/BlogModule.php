<?php

namespace App\Blog;

use Framework\Module;
use Framework\Router;
use App\Blog\Actions\BlogAction;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\AdminBlogAction;
use Psr\Container\ContainerInterface;
use App\Blog\Actions\CategoryCrudAction;
use Framework\Renderer\RendererInterface;

class BlogModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';
    const MIGRATIONS = __DIR__ . '/db/migrations';
    const SEEDS = __DIR__ . '/db/seeds';
    /**
     * __construct
     *
     * @param  Router $router
     *
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $blogPrefix = $container->get('blog.prefix');
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $router->get($blogPrefix, BlogAction::class, 'blog.index');
        $router->get("$blogPrefix/{slug:[a-z\-0-9]+}-{id:[0-9]+}", BlogAction::class, 'blog.show');
        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", PostCrudAction::class, 'blog.admin');
            $router->crud("$prefix/categories", CategoryCrudAction::class, 'blog.category.admin');
        }
    }
}
