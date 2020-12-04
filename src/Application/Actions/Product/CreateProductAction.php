<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/27
 * Time: 13:20
 */

namespace App\Application\Actions\Product;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Product\CreateProductChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateProductAction extends Action
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var CreateProductChain
     */
    private $chain;

    /**
     * CreateProductAction constructor.
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

        $this->chain = (new CreateProductChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Product", grants={"createProduct"})
     */
    protected function action(): Response
    {
        try {
            $product = $this->getRequestBody(true);
            $data = [
                'product' => array_merge($product, [
                    'storeId' => $this->request->getHeaderLine('storeId')
                ]),
                'storeId' => $this->request->getHeaderLine('storeId'),
                'categories_ids' => [$product['categoryId'] ?? '00000000-0000-0000-0000-000000000000'],
                'selections' => ['id']
            ];

            $response = $this->chain->run($data);
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}