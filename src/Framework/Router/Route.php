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

    public function __construct(string $name, callable $callback, array $parameters)
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
     * @return callback
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * retrieve the URL parameters
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
