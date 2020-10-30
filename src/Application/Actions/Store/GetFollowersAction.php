<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 16:05
 */

namespace App\Application\Actions\Store;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\Store\GetFollowersChain;
use App\Utilities\RequestSenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetFollowersAction extends Action
{
    /**
     * @var GetFollowersChain
     */
    private $chain;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * GetFollowersAction constructor.
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

        $this->chain = (new GetFollowersChain($requestSender, $tokenAuth, $request))->initiate();
        return parent::__invoke($request, $response, $args);
    }

    /**
     * @return Response
     *
     * @Permissions(policyModel="Store", grants={"listFollowers"})
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                [
                    'storeId' => $this->request->getAttribute('storeId'),
                    'pagination' => $this->request->getQueryParams()
                ]
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}