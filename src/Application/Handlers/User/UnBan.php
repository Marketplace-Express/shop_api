<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/10
 * Time: 15:22
 */

namespace App\Application\Handlers\User;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Psr\Log\LoggerInterface;

class UnBan extends AbstractHandler
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
     * UnBan constructor.
     * @param RequestSenderInterface $requestSender
     * @param LoggerInterface $logger
     */
    public function __construct(RequestSenderInterface $requestSender, LoggerInterface $logger)
    {
        $this->requestSender = $requestSender;
        $this->logger = $logger;
    }

    public function handle(array $data = [])
    {
        $this->requestSender->services->users->unBan($data['userId']);

        return parent::handle();
    }
}