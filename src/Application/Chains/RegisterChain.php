<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/29
 * Time: 01:08
 */

namespace App\Application\Chains;


use App\Application\Handlers\AbstractHandler;
use App\Application\Handlers\Register;
use App\Application\Handlers\Logger;
use App\Utilities\RequestSenderInterface;
use Psr\Log\LoggerInterface;

class RegisterChain
{
    /** @var AbstractHandler */
    private $chain;

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

    /**
     * @return $this
     */
    public function initiate()
    {
        $handler = new Register($this->requestSender);
        $handler->next(new Logger($this->logger, "New user registered"));

        $this->chain = $handler;

        return $this;
    }

    /**
     * @param array $data
     * @return array|mixed
     */
    public function run(array $data)
    {
        return $this->chain->handle($data);
    }
}