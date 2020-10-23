<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/04
 * Time: 13:14
 */

namespace App\Application\Chains\User;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\User\Ban;
use App\Application\Handlers\Logger;
use App\Utilities\RequestSenderInterface;
use Psr\Log\LoggerInterface;

class BanChain extends AbstractChain
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
     * BanChain constructor.
     * @param RequestSenderInterface $requestSender
     * @param LoggerInterface $logger
     */
    public function __construct(RequestSenderInterface $requestSender, LoggerInterface $logger)
    {
        $this->requestSender = $requestSender;
        $this->logger = $logger;
    }

    public function initiate()
    {
        $handlers = new Ban($this->requestSender);
        $handlers->next(new Logger($this->logger, "User banned"));

        $this->handlers = $handlers;

        return $this;
    }
}