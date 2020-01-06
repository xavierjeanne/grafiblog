<?php

namespace Framework\Renderer;

use Framework\Router\RouterTwigExtension;
use Psr\Container\ContainerInterface;

class TwigRendererFactory
{

    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $viewPath = $container->get('views.path');
        $loader = new \Twig\Loader\FilesystemLoader($viewPath);
        $twig = new \Twig\Environment($loader);
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extensions) {
                $twig->addExtension($extensions);
            }
        }
        return new TwigRenderer($loader, $twig);
    }
}
