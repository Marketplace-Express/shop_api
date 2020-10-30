<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 17:38
 */

namespace App\Application\Actions\Role;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Role\UnAssignRoleChain;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UnAssignRoleAction extends Action
{
    /**
     * @var UnAssignRoleChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UnAssignRoleAction constructor.
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

        $this->chain = (new UnAssignRoleChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Role", grants={"unAssignRole"})
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