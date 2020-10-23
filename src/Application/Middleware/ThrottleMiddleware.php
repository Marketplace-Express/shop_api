<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/14
 * Time: 23:29
 */

namespace App\Application\Middleware;


use App\Application\Actions\Action;
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
        $requestedAction = $request->getAttribute('requested_action_as_string');
        $requestedAction = explode('\\', $requestedAction);
        $requestedAction = join('\\', array_splice($requestedAction, 2, count($requestedAction) - 1));

        $clientIp = $request->getAttribute('ip_address');

        if (!$this->service->checkThrottle($clientIp, $requestedAction)) {
            return $this->respondWithError(new \Exception('limit exceeded', 429));
        }

        // Handle Request
        $response = $handler->handle($request);

        $requestedActionObject = $request->getAttribute('requested_action_as_object');

        if ($this->isSuccessResponse($response) || $this->forceLogUsage($requestedActionObject)) {
            // Record a try in case success response or force logging is on
            $this->service->addTry($clientIp, $requestedAction);
        }

        return $response;
    }

    /**
     * @param Action $actionClass
     * @return bool
     */
    private function forceLogUsage(Action $actionClass): bool
    {
        return $actionClass->forceLogUsage();
    }
}