<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 01:16
 */

namespace App\Application\Actions\User\Role;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\User\Role\UpdateRoleChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateRoleAction extends Action
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UpdateRoleChain
     */
    private $chain;

    /**
     * UpdateRoleAction constructor.
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

        $this->chain = (new UpdateRoleChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Role", grants={"updateRole"})
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                array_merge($this->getRequestBody(true), [
                    'role_id' => $this->request->getAttribute('roleId')
                ])
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}