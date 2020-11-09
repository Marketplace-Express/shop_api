<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/07
 * Time: 15:34
 */

namespace App\Application\Actions\Category;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Category\CreateCategoryChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateCategoryAction extends Action
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var CreateCategoryChain
     */
    private $chain;

    /**
     * CreateCategoryAction constructor.
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

        $this->chain = (new CreateCategoryChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Category", grants={"createCategory"})
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                array_merge($this->getRequestBody(true), [
                    'storeId' => $this->request->getHeaderLine('storeId')
                ])
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}