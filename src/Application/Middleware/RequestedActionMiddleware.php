<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/02
 * Time: 21:07
 */

namespace App\Application\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestedActionMiddleware extends AbstractMiddleware
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute('__route__');
        $requestedAction = $route->getCallable(); // called action as string
        $requestedActionObject = $route->getCallableResolver()->resolveRoute($requestedAction); // called action as object

        $request = $request->withAttribute('requested_action_as_string', $requestedAction);
        $request = $request->withAttribute('requested_action_as_object', reset($requestedActionObject));

        return $handler->handle($request);
    }
}