<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/29
 * Time: 01:08
 */

namespace App\Application\Chains\User\User;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\User\User\Login;
use App\Application\Handlers\Logger;
use App\Utilities\RequestSenderInterface;
use Psr\Log\LoggerInterface;

class LoginChain extends AbstractChain
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
     * LoginChain constructor.
     * @param RequestSenderInterface $requestSender
     * @param LoggerInterface $logger
     */
    public function __construct(RequestSenderInterface $requestSender, LoggerInterface $logger)
    {
        $this->requestSender = $requestSender;
        $this->logger = $logger;
    }

    /**
     * @return $this
     */
    public function initiate()
    {
        $handlers = new Logger($this->logger, "new user login");
        $handlers->next(new Login($this->requestSender));

        $this->handlers = $handlers;

        return $this;
    }
}