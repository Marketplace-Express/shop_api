<?php

namespace App\Application\Middleware;


use App\Application\Services\AuthService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Middleware\TokenAuthentication;

class AuthenticateMiddleware extends AbstractMiddleware
{
    /**
     * @var AuthService
     */
    private $service;
    /**
     * @var TokenAuthentication
     */
    private $tokenAuthentication;

    /**
     * Create a new middleware instance.
     *
     * @param AuthService $service
     * @param TokenAuthentication $tokenAuthentication
     */
    public function __construct(AuthService $service, TokenAuthentication $tokenAuthentication)
    {
        $this->service = $service;
        $this->tokenAuthentication = $tokenAuthentication;
    }

    /**
     * Handle an incoming request.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return mixed
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $this->tokenAuthentication->findToken($request);
        $csrfToken = $request->getHeaderLine('csrf-token');
        if (!$token || !$this->service->isAuthenticated($token, $csrfToken)) {
            return $this->respondWithError(new \Exception('unauthenticated', 401));
        }

        return $handler->handle($request);
    }
}
