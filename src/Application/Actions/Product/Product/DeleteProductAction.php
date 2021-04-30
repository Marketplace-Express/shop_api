<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/12/04
 * Time: 12:03
 */

namespace App\Application\Actions\Product\Product;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Product\Product\DeleteProductChain;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteProductAction extends Action
{
    /**
     * @var DeleteProductChain
     */
    private $chain;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * DeleteProductAction constructor.
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

        $this->chain = (new DeleteProductChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Product", grants={"deleteProduct"})
     */
    protected function action(): Response
    {
        try {
            $this->chain->run(
                ['productId' => $this->request->getAttribute('productId')]
            );
            $response = ['status' => StatusCodeInterface::STATUS_NO_CONTENT, 'message' => null];
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}