<?php

namespace Framework\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * add method for redirection router
 *
 * Trait RouterAwareAction
 * @package Framewor\Actions
 */
trait RouterAwareAction
{
    /**
     * send Response redirection
     *
     * @param string $path
     * @param array $params
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirectUri = $this->router->generateUri($path, $params);
        return (new Response())->withStatus(301)->withHeader('Location', $redirectUri);
    }
}
