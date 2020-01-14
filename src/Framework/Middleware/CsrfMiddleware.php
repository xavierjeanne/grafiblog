<?php

namespace Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Exception\CsrfInvalidException;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

class CsrfMiddleware implements MiddlewareInterface
{
    private $limit;
    private $formKey;
    private $sessionKey;
    private $session;

    public function __construct(&$session, int $limit = 50, string $formKey = '_csrf', string $sessionKey = 'csrf')
    {
        $this->validSession($session);
        $this->session = &$session;
        $this->limit = $limit;
        $this->sessionKey = $sessionKey;
        $this->formKey = $formKey;
    }
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody() ?: [];
            if (!array_key_exists($this->formKey, $params)) {
                $this->reject();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->useToken($params[$this->formKey]);
                    return $delegate->process($request);
                } else {
                    $this->reject();
                }
            }
        } else {
            return $delegate->process($request);
        }
    }


    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();
        return $token;
    }
    public function getFormKey(): string
    {
        return $this->formKey;
    }
    private function reject(): void
    {
        throw new CsrfInvalidException();
    }
    private function useToken($token): void
    {
        $tokens = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $token !== $t;
        });
        $this->session[$this->sessionKey] = $tokens;
    }

    private function limitTokens(): void
    {
        $tokens = $this->session[$this->sessionKey] ?? [];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }
    private function validSession($session)
    {
        if (!is_array($session) && !$session instanceof \ArrayAccess) {
            throw new \TypeError('This session send to middleware is not treatable like an array');
        }
    }
}
