<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/16
 * Time: 12:25
 */

namespace App\Application\Chains\User;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Logger;
use App\Application\Handlers\Store\IsStoreOwner;
use App\Application\Handlers\User\Authenticate;
use App\Application\Handlers\User\Authorize;
use App\Application\Handlers\User\GetBanned;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Middleware\TokenAuthentication;

class GetBannedChain extends AbstractChain
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
     * GetBannedChain constructor.
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
            ->next(new GetBanned($this->requestSender))
            ->next(new Logger($this->logger, "get banned users"));

        $this->handlers = $handlers;

        return $this;
    }
}