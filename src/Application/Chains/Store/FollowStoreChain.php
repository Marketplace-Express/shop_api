<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 13:37
 */

namespace App\Application\Chains\Store;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Logger;
use App\Application\Handlers\Store\FollowStore;
use App\Application\Handlers\Store\GetStore;
use App\Application\Handlers\User\Authenticate;
use App\Application\Handlers\User\Authorize;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Middleware\TokenAuthentication;

class FollowStoreChain extends AbstractChain
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FollowStoreChain constructor.
     * @param RequestSenderInterface $requestSender
     * @param TokenAuthentication $tokenAuthentication
     * @param ServerRequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestSenderInterface $requestSender,
        TokenAuthentication $tokenAuthentication,
        ServerRequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->requestSender = $requestSender;
        $this->tokenAuthentication = $tokenAuthentication;
        $this->request = $request;
        $this->logger = $logger;
    }

    public function initiate()
    {
        $handlers = new Authenticate($this->requestSender, $this->request, $this->tokenAuthentication);

        $handlers
            ->next(new GetStore($this->requestSender)) // check if store exists
            ->next(new FollowStore($this->requestSender))
            ->next(new Logger($this->logger, "user follow store"));

        $this->handlers = $handlers;

        return $this;
    }
}