<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/22
 * Time: 00:40
 */

namespace App\Application\Chains\Product;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Product\GetProductsForAdmin;
use App\Application\Handlers\Store\IsStoreOwner;
use App\Application\Handlers\User\Authenticate;
use App\Application\Handlers\User\Authorize;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\TokenAuthentication;

class GetProductsForAdminChain extends AbstractChain
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
     * GetProductsForAdminChain constructor.
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
            ->next(new GetProductsForAdmin($this->requestSender));

        $this->handlers = $handlers;

        return $this;
    }
}