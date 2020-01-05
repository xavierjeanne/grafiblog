<?php

namespace Tests\Framework\Modules;


class StringModule
{
    public function __contruct(\Framework\Router $router)
    {
        $router->get('/demo', function () {
            return 'DEMO';
        }, 'demo');
    }
}
