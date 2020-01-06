<?php

namespace Framework\Router;

/**
 * Class Route
 * represente a matched route
 */
class Route
{
    private $name;

    private $callback;

    private $parameters;

    /**
     * Route constructor
     *
     * @param  string $name
     * @param  string|callable $callback
     * @param  array $parameters
     *
     * @return void
     */
    public function __construct(string $name, $callback, array $parameters)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }
    /**
     * getName
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * getCallback
     *
     * @return string|callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * retrieve the URL parameters
     *
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
