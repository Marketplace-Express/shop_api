<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/03
 * Time: 19:30
 */

namespace App\Application\Middleware;


use App\Application\Actions\Permissions;
use App\Application\Services\AuthService;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Middleware\TokenAuthentication;
use Slim\Middleware\TokenAuthentication\TokenNotFoundException;
use const Grpc\STATUS_OK;

class AuthorizeMiddleware extends AbstractMiddleware
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
     * AuthorizeMiddleware constructor.
     * @param AuthService $service
     * @param TokenAuthentication $tokenAuthentication
     */
    public function __construct(AuthService $service, TokenAuthentication $tokenAuthentication)
    {
        $this->service = $service;
        $this->tokenAuthentication = $tokenAuthentication;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $token = $this->tokenAuthentication->findToken($request);
        } catch (TokenNotFoundException $exception) {
            return $this->respondWithError($exception, StatusCodeInterface::STATUS_UNAUTHORIZED);
        }

        $csrfToken = $request->getHeaderLine('csrf-token');

        $requestedAction = $request->getAttribute('requested_action_as_string');
        $permissions = $this->getActionPermissions($requestedAction);

        $isAuthorized = $this->service->isAuthorized(
            $token,
            $csrfToken,
            $permissions->grants,
            $permissions->operator
        );

        if (!$isAuthorized) {
            return $this->respondWithError(new \Exception('unauthorized', StatusCodeInterface::STATUS_UNAUTHORIZED));
        }

        return $handler->handle($request);
    }

    /**
     * @param string $calledAction
     * @return object|null
     */
    public function getActionPermissions(string $calledAction)
    {
        try {
            AnnotationRegistry::loadAnnotationClass($calledAction);
            $reflectionClass = new \ReflectionClass($calledAction);
            $method = $reflectionClass->getMethod('action');
            $reader = new AnnotationReader();
            return $reader->getMethodAnnotation(
                $method,
                Permissions::class
            ) ?? new Permissions();
        } catch (\Throwable $exception) {
            return null;
        }
    }
}