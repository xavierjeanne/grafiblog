<?php

namespace App\Auth\Actions;

use Framework\Router;
use App\Auth\DatabaseAuth;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAttemptAction
{
    private $renderer;
    private $auth;
    private $router;
    private $session;

    use RouterAwareAction;
    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, SessionInterface $session, Router $router)
    {
        $this->auth = $auth;
        $this->renderer = $renderer;
        $this->router = $router;
        $this->session = $session;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $user = $this->auth->login($params['username'], $params['password']);
        if ($user) {
            $path = $this->session->get('auth.redirect') ?: $this->router->generateUri('admin');
            $this->session->delete('auth.redirect');
            return new RedirectResponse($path);
        } else {
            (new FlashService($this->session))->error('Identifiant ou mot de passe incorrect');
            return $this->redirect('auth.login');
        }
    }
}
