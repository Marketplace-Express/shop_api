<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/10
 * Time: 15:21
 */

namespace App\Application\Chains\User;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Logger;
use App\Application\Handlers\User\UnBan;
use App\Utilities\RequestSenderInterface;
use Psr\Log\LoggerInterface;

class UnBanChain extends AbstractChain
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(RequestSenderInterface $requestSender, LoggerInterface $logger)
    {
        $this->requestSender = $requestSender;
        $this->logger = $logger;
    }

    public function initiate()
    {
        $handlers = new UnBan($this->requestSender, $this->logger);
        $handlers->next(new Logger($this->logger, "user unbanned"));

        $this->handlers = $handlers;

        return $this;
    }
}