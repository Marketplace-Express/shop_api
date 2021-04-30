<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/14
 * Time: 12:39
 */

namespace App\Application\Chains\Product\Product;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Logger;
use App\Application\Handlers\Product\Product\GetProduct;
use App\Application\Handlers\ReturnData;
use App\Utilities\RequestSenderInterface;
use Psr\Log\LoggerInterface;

class GetProductChain extends AbstractChain
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
     * GetProductChain constructor.
     * @param RequestSenderInterface $requestSender
     * @param LoggerInterface $logger
     */
    public function __construct(RequestSenderInterface $requestSender, LoggerInterface $logger) {
        $this->requestSender = $requestSender;
        $this->logger = $logger;
    }

    public function initiate()
    {
        $handlers = new GetProduct($this->requestSender);
        $handlers
            ->next(new Logger($this->logger, "get product", ['product']))
            ->next(new ReturnData("product"));

        $this->handlers = $handlers;

        return $this;
    }
}