<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/12/10
 * Time: 17:47
 */

namespace App\Application\Actions\User\User;


use App\Application\Actions\Action;
use App\Application\Chains\User\User\ProfileChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProfileAction extends Action
{
    /**
     * @var ProfileChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ProfileAction constructor.
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

        $this->chain = (new ProfileChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run();
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}