<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 00:43
 */

namespace App\Application\Actions\User\Role;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\User\Role\UnAssignPermissionChain;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UnAssignPermissionAction extends Action
{
    /**
     * @var UnAssignPermissionChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UnAssignPermissionAction constructor.
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

        $this->chain = (new UnAssignPermissionChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Role", grants={"unAssignPermission"})
     */
    protected function action(): Response
    {
        try {
            $this->chain->run(
                array_merge($this->getRequestBody(true), [
                    'roleId' => $this->request->getAttribute('roleId')
                ])
            );

            $response = ['status' => StatusCodeInterface::STATUS_NO_CONTENT, 'message' => ''];
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}