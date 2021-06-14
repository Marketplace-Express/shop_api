<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/12
 * Time: 15:18
 */

namespace App\Application\Chains\Product\Product;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Product\Product\SearchProduct;
use App\Utilities\RequestSenderInterface;

class SearchProductChain extends AbstractChain
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * SearchProductChain constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender) {
        $this->requestSender = $requestSender;
    }

    public function initiate()
    {
        $this->handlers = new SearchProduct($this->requestSender);

        return $this;
    }
}