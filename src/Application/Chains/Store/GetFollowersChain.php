<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 16:08
 */

namespace App\Application\Chains\Store;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Store\GetFollowers;
use App\Application\Handlers\Store\IsStoreOwner;
use App\Application\Handlers\User\User\Authenticate;
use App\Application\Handlers\User\User\Authorize;
use App\Application\Handlers\User\User\GetUsersByIds;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\TokenAuthentication;

class GetFollowersChain extends AbstractChain
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * @var TokenAuthentication
     */
    private $tokenAuthentication;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * GetFollowersChain constructor.
     * @param RequestSenderInterface $requestSender
     * @param TokenAuthentication $tokenAuthentication
     * @param ServerRequestInterface $request
     */
    public function __construct(
        RequestSenderInterface $requestSender,
        TokenAuthentication $tokenAuthentication,
        ServerRequestInterface $request
    ) {
        $this->requestSender = $requestSender;
        $this->tokenAuthentication = $tokenAuthentication;
        $this->request = $request;
    }

    public function initiate()
    {
        $storeId = $this->request->getAttribute('storeId');

        $handlers = new Authenticate($this->requestSender, $this->request, $this->tokenAuthentication);

        $handlers
            ->next(new IsStoreOwner($this->requestSender, $storeId))
            ->next(new Authorize($this->requestSender, $this->request, $this->tokenAuthentication, ['storeId' => $storeId]))
            ->next(new GetFollowers($this->requestSender))
            ->next(new GetUsersByIds($this->requestSender));

        $this->handlers = $handlers;

        return $this;
    }
}