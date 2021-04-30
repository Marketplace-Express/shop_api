<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/12/04
 * Time: 16:49
 */

namespace App\Application\Actions\Product\Product;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Product\Product\UpdateQuantityChain;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateProductQuantityAction extends Action
{
    /**
     * @var UpdateQuantityChain
     */
    private $chain;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UpdateProductQuantityAction constructor.
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

        $this->chain = (new UpdateQuantityChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Product", grants={"updateProductQuantity"})
     */
    protected function action(): Response
    {
        try {
            $this->chain->run(
                array_merge($this->getRequestBody(true), [
                    'productId' => $this->request->getAttribute('productId')
                ])
            );
            $response = ['status' => StatusCodeInterface::STATUS_NO_CONTENT, 'message' => null];
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}