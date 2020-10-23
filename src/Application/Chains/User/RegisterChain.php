<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/29
 * Time: 01:08
 */

namespace App\Application\Chains\User;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\User\Register;
use App\Application\Handlers\Logger;
use App\Utilities\RequestSenderInterface;
use Psr\Log\LoggerInterface;

class RegisterChain extends AbstractChain
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
     * RegisterChain constructor.
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
        $handlers = new Register($this->requestSender);
        $handlers->next(new Logger($this->logger, "New user registered"));

        $this->handlers = $handlers;

        return $this;
    }
}