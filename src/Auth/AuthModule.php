<?php

namespace App\Auth;

use Framework\Module;
use Framework\Router;
use Framework\Router\Route;
use App\Auth\Actions\LoginAction;
use App\Blog\Actions\PostIndexAction;
use Psr\Container\ContainerInterface;
use App\Auth\Actions\LoginAttemptAction;
use App\Auth\Actions\LogoutAction;
use Framework\Renderer\RendererInterface;

class AuthModule extends Module
{


    const DEFINITIONS = __DIR__ . '/config.php';
    const MIGRATIONS = __DIR__ . '/db/migrations';
    const SEEDS = __DIR__ . '/db/seeds';
    public function __construct(Router $router, ContainerInterface $container, RendererInterface $renderer)
    {
        $renderer->addPath('auth', __DIR__ . '/views');
        $router->get($container->get('auth.login'), LoginAction::class, 'auth.login');
        $router->post($container->get('auth.login'), LoginAttemptAction::class);
        $router->post('/logout', LogoutAction::class, 'auth.logout');
    }
}
