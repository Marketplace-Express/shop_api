<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/29
 * Time: 01:08
 */

namespace App\Application\Chains\User\User;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\User\User\Register;
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
        $data['first_name'] = $data['first_name'] ?? null;
        $data['last_name'] = $data['last_name'] ?? null;
        $data['gender'] = $data['gender'] ?? null;
        $data['birthdate'] = $data['birthdate'] ?? null;
        $data['password'] = $data['password'] ?? null;
        $data['email'] = $data['email'] ?? null;

        $handlers = new Logger($this->logger, "New user registered");
        $handlers->next(new Register($this->requestSender));

        $this->handlers = $handlers;

        return $this;
    }
}