<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/31
 * Time: 10:12
 */

namespace App\Application\Actions\Store;


use App\Application\Actions\Action;
use App\Application\Chains\Store\UnFollowStoreChain;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class UnFollowStoreAction extends Action
{
    /**
     * @var UnFollowStoreChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UnFollowStoreAction constructor.
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

        $this->chain = (new UnFollowStoreChain($requestSender, $tokenAuth, $request, $logger))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        try {
            $this->chain->run(
                $this->getRequestBody(true)
            );

            $response = ['status' => StatusCodeInterface::STATUS_NO_CONTENT, 'message' => ''];
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}