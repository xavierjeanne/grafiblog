<?php

namespace App\Auth\Actions;

use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAction
{
    private $renderer;
    public function __construct(RendererInterface $renderer)
    {

        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->renderer->render('@auth/login');
    }
}
