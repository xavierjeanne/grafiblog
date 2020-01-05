<?php

namespace Tests\Framework\Modules;


class ErroredModule
{
    public function __contruct(\Framework\Router $router)
    {
        $router->get('/demo', function () {
            return new \stdClass();
        }, 'demo');
    }
}
