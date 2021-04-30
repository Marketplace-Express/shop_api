<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/16
 * Time: 12:24
 */

namespace App\Application\Actions\User\User;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\User\User\GetBannedChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class GetBannedAction extends Action
{
    /**
     * @var GetBannedChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * GetBannedAction constructor.
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

        $this->chain = (new GetBannedChain($requestSender, $logger, $tokenAuth, $request))->initiate();

        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="User")
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                $this->request->getQueryParams()
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}