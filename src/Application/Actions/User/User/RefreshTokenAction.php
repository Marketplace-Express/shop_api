<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/13
 * Time: 15:54
 */

namespace App\Application\Actions\User\User;


use App\Application\Actions\Action;
use App\Application\Chains\User\User\RefreshTokenChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RefreshTokenAction extends Action
{
    /**
     * @var RefreshTokenChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * RefreshTokenAction constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $requestSender = $this->container->get(RequestSenderInterface::class);
        $this->chain = (new RefreshTokenChain($requestSender))->initiate();

        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                $this->getRequestBody(true)
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}