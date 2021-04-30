<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 17:35
 */

namespace App\Application\Actions\User\Role;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\User\Role\AssignRoleChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AssignRoleAction extends Action
{
    /**
     * @var AssignRoleChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AssignRoleAction constructor.
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

        $this->chain = (new AssignRoleChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Role", grants={"assignRole"})
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                array_merge($this->getRequestBody(true), [
                    'roleId' => $this->request->getAttribute('roleId')
                ])
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}