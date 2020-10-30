<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 14:37
 */

namespace App\Application\Actions\Store;


use App\Application\Actions\Action;
use App\Application\Chains\Store\CreateStoreChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class CreateStoreAction extends Action
{
    /**
     * @var CreateStoreChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * CreateStoreAction constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $requestSender = $this->container->get(RequestSenderInterface::class);
        $tokenAuth = $this->container->get('tokenAuth');
        $logger = $this->container->get(LoggerInterface::class);

        $this->chain = (new CreateStoreChain($requestSender, $tokenAuth, $request, $logger))->initiate();
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