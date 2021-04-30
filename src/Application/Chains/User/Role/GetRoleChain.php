<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/17
 * Time: 10:49
 */

namespace App\Application\Chains\User\Role;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\User\Role\GetRole;
use App\Application\Handlers\Store\IsStoreOwner;
use App\Application\Handlers\User\User\Authenticate;
use App\Application\Handlers\User\User\Authorize;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\TokenAuthentication;

class GetRoleChain extends AbstractChain
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var TokenAuthentication
     */
    private $tokenAuthentication;

    public function __construct(
        RequestSenderInterface $requestSender,
        ServerRequestInterface $request,
        TokenAuthentication $tokenAuthentication
    ) {
        $this->requestSender = $requestSender;
        $this->request = $request;
        $this->tokenAuthentication = $tokenAuthentication;
    }

    public function initiate()
    {
        $storeId = $this->request->getHeaderLine('storeId');

        $handlers = new Authenticate($this->requestSender, $this->request, $this->tokenAuthentication);

        $handlers
            ->next(new IsStoreOwner($this->requestSender, $storeId))
            ->next(new Authorize($this->requestSender, $this->request, $this->tokenAuthentication, ['storeId' => $storeId]))
            ->next(new GetRole($this->requestSender));

        $this->handlers = $handlers;

        return $this;
    }
}