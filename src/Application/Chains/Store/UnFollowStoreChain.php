<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/31
 * Time: 10:00
 */

namespace App\Application\Chains\Store;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Logger;
use App\Application\Handlers\Store\UnFollowStore;
use App\Application\Handlers\User\Authenticate;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Middleware\TokenAuthentication;

class UnFollowStoreChain extends AbstractChain
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
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UnFollowStoreChain constructor.
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
            ->next(new UnFollowStore($this->requestSender))
            ->next(new Logger($this->logger, 'user unfollow store'));

        $this->handlers = $handlers;

        return $this;
    }
}