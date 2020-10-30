<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 00:35
 */

namespace App\Application\Actions\Store;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Store\DeleteStoreChain;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class DeleteStoreAction extends Action
{
    /**
     * @var DeleteStoreChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * DeleteStoreAction constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $requestSender = $this->container->get(RequestSenderInterface::class);
        $logger = $this->container->get(LoggerInterface::class);
        $tokenAuth = $this->container->get('tokenAuth');

        $this->chain = (new DeleteStoreChain($requestSender, $tokenAuth, $request, $logger))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Store", grants={"deleteStore"})
     */
    protected function action(): Response
    {
        try {
            $this->chain->run(
                ['storeId' => $this->request->getAttribute('storeId')]
            );

            $response = ['status' => StatusCodeInterface::STATUS_NO_CONTENT, 'message' => ''];
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}