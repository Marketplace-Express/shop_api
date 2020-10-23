<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/17
 * Time: 10:49
 */

namespace App\Application\Chains\Role;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Role\GetRole;
use App\Application\Handlers\User\Authorize;
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
        $handlers = new Authorize(
            $this->requestSender,
            $this->request,
            $this->tokenAuthentication,
            ['roleId' => $this->request->getAttribute('roleId')]
        );
        $handlers->next(new GetRole($this->requestSender));

        $this->handlers = $handlers;

        return $this;
    }
}