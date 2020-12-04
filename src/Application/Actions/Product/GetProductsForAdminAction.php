<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/22
 * Time: 00:42
 */

namespace App\Application\Actions\Product;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Product\GetProductsForAdminChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetProductsForAdminAction extends Action
{
    /**
     * @var GetProductsForAdminChain
     */
    private $chain;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * GetProductsForAdminAction constructor.
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

        $this->chain = (new GetProductsForAdminChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Product", grants={"listProducts"})
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                array_merge($this->request->getQueryParams(), [
                    'storeId' => $this->request->getHeaderLine('storeId')
                ])
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}