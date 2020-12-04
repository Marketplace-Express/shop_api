<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/10
 * Time: 12:13
 */

namespace App\Application\Actions\Category;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Category\UpdateCategoryChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateCategoryAction extends Action
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UpdateCategoryChain
     */
    private $chain;

    /**
     * UpdateCategoryAction constructor.
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

        $this->chain = (new UpdateCategoryChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Category", grants={"updateCategory"})
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