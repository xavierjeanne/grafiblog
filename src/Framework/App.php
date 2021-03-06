<?php

namespace Framework;

use Exception;
use Framework\Router;
use DI\ContainerBuilder;
use PHP_CodeSniffer\Util\Common;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Middleware\RoutePrefixMiddleware;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

class App implements DelegateInterface
{
    /**
     * List of modules
     *
     * @var array
     */
    private $modules = [];

    /**
     *
     * @var string
     */
    private $definition;
    /**
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     *
     * @var string[]
     */
    private $middlewares;

    /**
     * Undocumented variable
     *
     * @var integer
     */
    private $index = 0;

    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }
    /**
     * add module to application
     *
     * @param string $module
     * @return self
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Add a middleware
     *
     * @param  mixed $routePrefix
     * @param  mixed $middleware
     *
     * @return App
     */
    public function pipe(string $routePrefix, ?string $middleware = null): self
    {
        if ($middleware === null) {
            $this->middlewares[] = $routePrefix;
        } else {
            $this->middlewares[] = new RoutePrefixMiddleware($this->getContainer(), $routePrefix, $middleware);
        }
        return $this;
    }

    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new \Exception('No middleware intercept this request');
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'process']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->process($request);
    }

    /**
     * Undocumented function
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $builder->addDefinitions($this->definition);

            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }
        return $this->container;
    }

    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }
        return null;
    }

    public function getModules(): array
    {
        return $this->modules;
    }
}
