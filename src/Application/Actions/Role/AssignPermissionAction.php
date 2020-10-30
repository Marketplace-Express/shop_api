<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/24
 * Time: 13:50
 */

namespace App\Application\Actions\Role;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Role\AssignPermissionChain;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class AssignPermissionAction extends Action
{
    /**
     * @var AssignPermissionChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AssignPermissionAction constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $requestSender = $this->container->get(RequestSenderInterface::class);
        $logger = $this->container->get(LoggerInterface::class);
        $tokenAuth = $this->container->get('tokenAuth');

        $this->chain = (new AssignPermissionChain($requestSender, $logger, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Role", grants={"assignPermissions"})
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