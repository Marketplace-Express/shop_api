<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/10
 * Time: 14:36
 */

namespace App\Application\Actions\User\User;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\User\User\UnBanChain;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class UnBanAction extends Action
{
    /**
     * @var UnBanChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UnBanAction constructor.
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

        $this->chain = (new UnBanChain($requestSender, $logger, $tokenAuth, $request))->initiate();

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
            $this->chain->run(
                ['userId' => $this->request->getAttribute('userId')]
            );

            $response = ['status' => StatusCodeInterface::STATUS_NO_CONTENT, 'message' => ''];
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}