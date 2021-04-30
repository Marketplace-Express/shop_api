<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/12/10
 * Time: 17:48
 */

namespace App\Application\Chains\User\User;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\DataHandler;
use App\Application\Handlers\Logger;
use App\Application\Handlers\ReturnData;
use App\Application\Handlers\Store\GetFollowedStores;
use App\Application\Handlers\Store\GetStore;
use App\Application\Handlers\User\User\Authenticate;
use App\Application\Handlers\User\User\GetUsersByIds;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\TokenAuthentication;

class ProfileChain extends AbstractChain
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
     * ProfileChain constructor.
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

        $handlers
            ->next(new DataHandler(function ($data) {
                $data['users_ids'] = [$data['user']['user_id']];
                return $data;
            }))
            ->next(new GetUsersByIds($this->requestSender))
            ->next(new DataHandler(function ($data) {
                $data['profile'] = array_shift($data['users']);
                return $data;
            }))
            ->next(new ReturnData('profile'));

        $this->handlers = $handlers;

        return $this;
    }
}