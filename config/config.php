<?php

namespace config;

use PDO;
use Framework\Router;
use Framework\Session\PHPSession;
use Framework\Twig\CsrfExtension;
use Framework\Twig\FormExtension;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;
use Framework\Twig\FlashExtension;
use Framework\Router\RouterFactory;
use Framework\Session\SessionInterface;
use Framework\Twig\PagerfantaExtension;
use Framework\Middleware\CsrfMiddleware;
use Framework\Renderer\RendererInterface;
use Framework\Router\RouterTwigExtension;
use Framework\Renderer\TwigRendererFactory;


return [
    'env' => \DI\env('ENV', 'dev'),
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => '',
    'database.name' => 'grafiblog',
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        \DI\get(RouterTwigExtension::class),
        \DI\get(PagerfantaExtension::class),
        \DI\get(TextExtension::class),
        \DI\get(TimeExtension::class),
        \DI\get(FlashExtension::class),
        \DI\get(FormExtension::class),
        \DI\get(CsrfExtension::class)
    ],
    SessionInterface::class => \DI\create(PHPSession::class),
    CsrfMiddleware::class => \DI\create()->constructor(\di\get(SessionInterface::class)),
    Router::class => \DI\Factory(RouterFactory::class),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    \PDO::class => function (\Psr\Container\ContainerInterface $c) {
        return new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
];
