<?php

namespace Framework\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;

class RoutePrefixMiddleware implements MiddlewareInterface
{
    private $container;
    private $prefix;
    private $middleware;

    public function __construct(ContainerInterface $container, string $prefix, string $middleware)
    {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->middleware = $middleware;
    }
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        if (strpos($path, $this->prefix) === 0) {
            return $this->container->get($this->middleware)->process($request, $delegate);
        }
        return $delegate->process($request);
    }
}
