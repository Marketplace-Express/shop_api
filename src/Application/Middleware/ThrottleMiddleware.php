<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/14
 * Time: 23:29
 */

namespace App\Application\Middleware;


use App\Application\Services\ThrottleService;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class ThrottleMiddleware implements MiddlewareInterface
{
    /**
     * @var ThrottleService
     */
    private $service;

    /**
     * ThrottleMiddleware constructor.
     * @param ThrottleService $service
     */
    public function __construct(ThrottleService $service)
    {
        $this->service = $service;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestedAction = $request->getAttribute('requested_action');
        $clientIp = $request->getAttribute('ip_address');

        if (!$this->service->checkThrottle($clientIp, $requestedAction)) {
            $response = new Response(400);
            $response->getBody()->write('limit exceeded');
            return $response;
        }

        // Handle Request
        $response = $handler->handle($request);

        if ($this->isSuccessResponse($response)) {
            $tries = $this->service->getThrottleValue($clientIp, $requestedAction)[1];
            $this->service->set($clientIp, $requestedAction, ++$tries, 86400);
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    private function isSuccessResponse(ResponseInterface $response): bool
    {
        return substr($response->getStatusCode(), 0, 1) == substr(StatusCodeInterface::STATUS_OK, 0, 1);
    }
}