<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/22
 * Time: 00:21
 */

namespace App\Application\Chains\Product\Product;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Product\Product\GetProducts;
use App\Utilities\RequestSenderInterface;

class GetProductsChain extends AbstractChain
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * GetProductsChain constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function initiate()
    {
        $this->handlers = new GetProducts($this->requestSender);

        return $this;
    }
}