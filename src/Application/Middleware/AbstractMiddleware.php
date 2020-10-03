<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/03
 * Time: 11:03
 */

namespace App\Application\Middleware;


use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Response;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    /**
     * @param \Throwable $exception
     * @return Response
     */
    public function respondWithError(\Throwable $exception)
    {
        $response = new Response($exception->getCode());
        $errorMessage = [
            'statusCode' => $exception->getCode(),
            'data' => $exception->getMessage()
        ];
        $response->getBody()->write(json_encode($errorMessage, JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    protected function isSuccessResponse(ResponseInterface $response): bool
    {
        return substr($response->getStatusCode(), 0, 1) == substr(StatusCodeInterface::STATUS_OK, 0, 1);
    }
}