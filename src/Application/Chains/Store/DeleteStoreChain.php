<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 00:29
 */

namespace App\Application\Chains\Store;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Logger;
use App\Application\Handlers\Store\DeleteStore;
use App\Application\Handlers\Store\IsStoreOwner;
use App\Application\Handlers\User\Authenticate;
use App\Application\Handlers\User\Authorize;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Middleware\TokenAuthentication;

class DeleteStoreChain extends AbstractChain
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
     * DeleteStoreChain constructor.
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
        $storeId = $this->request->getAttribute('storeId');

        $handlers = new Authenticate($this->requestSender, $this->request, $this->tokenAuthentication);

        $handlers
            ->next(new IsStoreOwner($this->requestSender, $storeId))
            ->next(new Authorize($this->requestSender, $this->request, $this->tokenAuthentication, ['storeId' => $storeId]))
            ->next(new Logger($this->logger, "store deleted"))
            ->next(new DeleteStore($this->requestSender));

        $this->handlers = $handlers;

        return $this;
    }
}