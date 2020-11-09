<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/31
 * Time: 00:09
 */

namespace App\Application\Chains\Store;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Store\GetFollowedStores;
use App\Application\Handlers\User\Authenticate;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\TokenAuthentication;

class GetFollowedStoresChain extends AbstractChain
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
     * GetFollowedStoresChain constructor.
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
        $handlers = new Authenticate($this->requestSender, $this->request, $this->tokenAuthentication);

        $handlers->next(new GetFollowedStores($this->requestSender));

        $this->handlers = $handlers;

        return $this;
    }
}