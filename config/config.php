<?php

namespace config;

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;


return [

    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        \DI\get(\Framework\Router\RouterTwigExtension::class)
    ],
    Router::class => \DI\create(),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class)
];
