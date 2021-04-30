<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/04
 * Time: 13:14
 */

namespace App\Application\Chains\User\User;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Store\IsStoreOwner;
use App\Application\Handlers\User\User\Authenticate;
use App\Application\Handlers\User\User\Authorize;
use App\Application\Handlers\User\User\BanUser;
use App\Application\Handlers\Logger;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Middleware\TokenAuthentication;

class BanChain extends AbstractChain
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TokenAuthentication
     */
    private $tokenAuthentication;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * BanChain constructor.
     * @param RequestSenderInterface $requestSender
     * @param LoggerInterface $logger
     * @param TokenAuthentication $tokenAuthentication
     * @param ServerRequestInterface $request
     */
    public function __construct(
        RequestSenderInterface $requestSender,
        LoggerInterface $logger,
        TokenAuthentication $tokenAuthentication,
        ServerRequestInterface $request
    ) {
        $this->requestSender = $requestSender;
        $this->logger = $logger;
        $this->tokenAuthentication = $tokenAuthentication;
        $this->request = $request;
    }

    public function initiate()
    {
        $handlers = new Authenticate($this->requestSender, $this->request, $this->tokenAuthentication);

        $handlers
            ->next(new IsStoreOwner($this->requestSender, $this->request->getHeaderLine('storeId')))
            ->next(new Authorize($this->requestSender, $this->request, $this->tokenAuthentication))
            ->next(new Logger($this->logger, "user banned", ['userId', 'reason']))
            ->next(new BanUser($this->requestSender));

        $this->handlers = $handlers;

        return $this;
    }
}