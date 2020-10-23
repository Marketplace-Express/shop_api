<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/23
 * Time: 13:23
 */

namespace App\Application\Chains\Role;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Logger;
use App\Application\Handlers\Role\CreateRole;
use App\Application\Handlers\Store\GetStore;
use App\Application\Handlers\User\Authorize;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Middleware\TokenAuthentication;

class CreateRoleChain extends AbstractChain
{
    /**
     * @var LoggerInterface
     */
    private $logger;

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

    /**
     * CreateRoleChain constructor.
     * @param LoggerInterface $logger
     * @param RequestSenderInterface $requestSender
     * @param ServerRequestInterface $request
     * @param TokenAuthentication $tokenAuthentication
     */
    public function __construct(
        LoggerInterface $logger,
        RequestSenderInterface $requestSender,
        ServerRequestInterface $request,
        TokenAuthentication $tokenAuthentication
    ) {
        $this->logger = $logger;
        $this->requestSender = $requestSender;
        $this->request = $request;
        $this->tokenAuthentication = $tokenAuthentication;
    }

    /**
     * @return $this
     */
    public function initiate()
    {
        $handlers = new Authorize(
            $this->requestSender,
            $this->request,
            $this->tokenAuthentication,
            ['storeId' => $this->getStoreIdFromRequest()]
        );

        $handlers
            ->next(new GetStore($this->requestSender)) // check if store exists
            ->next(new CreateRole($this->requestSender))
            ->next(new Logger($this->logger, "new role created"));

        $this->handlers = $handlers;

        return $this;
    }

    /**
     * @return string
     */
    private function getStoreIdFromRequest(): string
    {
        $storeId = '';
        $authorizeData = json_decode($this->request->getBody()->getContents(), true);

        if ($authorizeData) {
            $storeId = array_key_exists('store_id', $authorizeData) ? $authorizeData['store_id'] : '';
        }

        return $storeId;
    }
}