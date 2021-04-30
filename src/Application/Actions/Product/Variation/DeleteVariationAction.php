<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/12/05
 * Time: 17:57
 */

namespace App\Application\Actions\Product\Variation;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Product\Variation\DeleteVariationChain;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class DeleteVariationAction extends Action
{
    /**
     * @var DeleteVariationChain
     */
    private $chain;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * DeleteVariationAction constructor.
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

        $this->chain = (new DeleteVariationChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Product", grants={"deleteProductVariation"})
     */
    protected function action(): Response
    {
        try {
            $this->chain->run(
                ['variationId' => $this->request->getAttribute('variationId')]
            );
            $response = ['status' => StatusCodeInterface::STATUS_NO_CONTENT, 'message' => null];
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}