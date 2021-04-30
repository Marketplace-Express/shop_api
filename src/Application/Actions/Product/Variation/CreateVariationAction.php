<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/12/05
 * Time: 00:06
 */

namespace App\Application\Actions\Product\Variation;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Product\Variation\CreateVariationChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateVariationAction extends Action
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var CreateVariationChain
     */
    private $chain;

    /**
     * CreateVariationAction constructor.
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

        $this->chain = (new CreateVariationChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Product", grants={"createProductVariation"})
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