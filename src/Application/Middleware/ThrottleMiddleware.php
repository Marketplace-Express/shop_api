<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/14
 * Time: 23:29
 */

namespace App\Application\Middleware;


use App\Application\Services\ThrottleService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ThrottleMiddleware extends AbstractMiddleware
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
            return $this->respondWithError(new \Exception('limit exceeded', 429));
        }

        // Handle Request
        $response = $handler->handle($request);

        if ($this->isSuccessResponse($response)) {
            // Record a try in case success response only
            $tries = $this->service->getThrottleValue($clientIp, $requestedAction)[1];
            $this->service->set($clientIp, $requestedAction, ++$tries, 86400);
        }

        return $response;
    }
}