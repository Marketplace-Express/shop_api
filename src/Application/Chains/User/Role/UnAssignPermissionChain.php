<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 00:44
 */

namespace App\Application\Chains\User\Role;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\User\Role\UnAssignPermission;
use App\Application\Handlers\Store\IsStoreOwner;
use App\Application\Handlers\User\User\Authenticate;
use App\Application\Handlers\User\User\Authorize;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\TokenAuthentication;

class UnAssignPermissionChain extends AbstractChain
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
     * UnAssignPermissionChain constructor.
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
        $storeId = $this->request->getHeaderLine('storeId');

        $handlers = new Authenticate($this->requestSender, $this->request, $this->tokenAuthentication);

        $handlers
            ->next(new IsStoreOwner($this->requestSender, $storeId))
            ->next(new Authorize($this->requestSender, $this->request, $this->tokenAuthentication, ['storeId' => $storeId]))
            ->next(new UnAssignPermission($this->requestSender));

        $this->handlers = $handlers;

        return $this;
    }
}