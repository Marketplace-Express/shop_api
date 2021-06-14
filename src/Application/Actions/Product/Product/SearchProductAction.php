<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/12
 * Time: 15:28
 */

namespace App\Application\Actions\Product\Product;


use App\Application\Actions\Action;
use App\Application\Chains\Product\Product\SearchProductChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SearchProductAction extends Action
{
    /**
     * @var SearchProductChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * SearchProductAction constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $requestSender = $this->container->get(RequestSenderInterface::class);

        $this->chain = (new SearchProductChain($requestSender))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                $this->request->getQueryParams()
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}