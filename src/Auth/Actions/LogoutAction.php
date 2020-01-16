<?php

namespace App\Auth\Actions;

use App\Auth\DatabaseAuth;
use Framework\Response\RedirectResponse;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class LogoutAction
{
    private $renderer;
    private $auth;
    private $flashService;
    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, FlashService $flashService)
    {
        $this->auth = $auth;
        $this->renderer = $renderer;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $this->auth->logout();
        $this->flashService->success('Vous ^vous êtes déconnecté avec succés');
        return new RedirectResponse('/blog');
    }
}
