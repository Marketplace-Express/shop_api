<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/27
 * Time: 12:56
 */

namespace App\Application\Actions\Product;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Product\GetProductForAdminChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetProductForAdminAction extends Action
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var GetProductForAdminChain
     */
    private $chain;

    /**
     * GetProductForAdminAction constructor.
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

        $this->chain = (new GetProductForAdminChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Product", grants={"viewProduct"})
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                ['productId' => $this->request->getAttribute('productId')]
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}