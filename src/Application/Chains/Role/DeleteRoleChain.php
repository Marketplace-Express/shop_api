<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/24
 * Time: 01:40
 */

namespace App\Application\Chains\Role;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Logger;
use App\Application\Handlers\Role\DeleteRole;
use App\Application\Handlers\Store\IsStoreOwner;
use App\Application\Handlers\User\Authenticate;
use App\Application\Handlers\User\Authorize;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Middleware\TokenAuthentication;

class DeleteRoleChain extends AbstractChain
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
     * DeleteRoleChain constructor.
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
        $storeId = $this->request->getHeaderLine('storeId');

        $handlers = new Authenticate($this->requestSender, $this->request, $this->tokenAuthentication);

        $handlers
            ->next(new IsStoreOwner($this->requestSender, $storeId))
            ->next(new Authorize($this->requestSender, $this->request, $this->tokenAuthentication, ['storeId' => $storeId]))
            ->next(new DeleteRole($this->requestSender))
            ->next(new Logger($this->logger, "role deleted"));

        $this->handlers = $handlers;

        return $this;
    }
}