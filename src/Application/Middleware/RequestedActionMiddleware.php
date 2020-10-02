<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/02
 * Time: 21:07
 */

namespace App\Application\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestedActionMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestedAction = explode('\\', $request->getAttribute('__route__')->getCallable()); // called action
        $requestedAction = join('\\', array_splice($requestedAction, 2, count($requestedAction) - 1));

        $request = $request->withAttribute('requested_action', $requestedAction);

        return $handler->handle($request);
    }
}