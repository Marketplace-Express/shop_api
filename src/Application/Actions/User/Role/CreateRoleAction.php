<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/23
 * Time: 13:21
 */

namespace App\Application\Actions\User\Role;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\User\Role\CreateRoleChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class CreateRoleAction extends Action
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var CreateRoleChain
     */
    private $chain;

    /**
     * CreateRoleAction constructor.
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

        $this->chain = (new CreateRoleChain($logger, $requestSender, $request, $tokenAuth))->initiate();

        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Role", grants={"createRole"})
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