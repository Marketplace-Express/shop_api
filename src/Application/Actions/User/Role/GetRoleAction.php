<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/17
 * Time: 10:48
 */

namespace App\Application\Actions\User\Role;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\User\Role\GetRoleChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;

class GetRoleAction extends Action
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $chain;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface {
        $requestSender = $this->container->get(RequestSenderInterface::class);
        $tokenAuthentication = $this->container->get('tokenAuth');

        $this->chain = (new GetRoleChain($requestSender, $request, $tokenAuthentication))->initiate();

        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Role", grants={"viewRole"})
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                ['roleId' => $this->request->getAttribute('roleId')]
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}