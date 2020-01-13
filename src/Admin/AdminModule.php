<?php

namespace App\Admin;

use Framework\Module;
use Framework\Router;
use App\Admin\AdminTwigExtension;
use Framework\Renderer\TwigRenderer;
use Framework\Renderer\RendererInterface;

class AdminModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(RendererInterface $renderer, Router $router, AdminTwigExtension $adminTwigExtension, string $prefix)
    {
        $renderer->addPath('admin', __DIR__ . '/views');
        $router->get($prefix, DashBoardAction::class, 'admin');
        if ($renderer instanceof TwigRenderer) {
            $renderer->getTwig()->addExtension($adminTwigExtension);
        }
    }
}
