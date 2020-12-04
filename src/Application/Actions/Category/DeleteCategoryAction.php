<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/10
 * Time: 13:24
 */

namespace App\Application\Actions\Category;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Category\DeleteCategoryChain;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteCategoryAction extends Action
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var DeleteCategoryChain
     */
    private $chain;

    /**
     * DeleteCategoryAction constructor.
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

        $this->chain = (new DeleteCategoryChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Category", grants={"deleteCategory"})
     */
    protected function action(): Response
    {
        try {
            $this->chain->run(
                array_merge($this->getRequestBody(true), [
                    'storeId' => $this->request->getHeaderLine('storeId')
                ])
            );

            $response = ['status' => StatusCodeInterface::STATUS_NO_CONTENT, 'message' => null];
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}