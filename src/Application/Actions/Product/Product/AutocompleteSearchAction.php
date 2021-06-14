<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/12
 * Time: 15:41
 */

namespace App\Application\Actions\Product\Product;


use App\Application\Actions\Action;
use App\Application\Chains\Product\Product\AutocompleteProductChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AutocompleteSearchAction extends Action
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var AutocompleteProductChain
     */
    private $chain;

    /**
     * AutocompleteSearchAction constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $requestSender = $this->container->get(RequestSenderInterface::class);
        $this->chain = (new AutocompleteProductChain($requestSender))->initiate();

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