<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/12
 * Time: 15:41
 */

namespace App\Application\Chains\Product\Product;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Product\Product\AutocompleteSearchHandler;
use App\Utilities\RequestSenderInterface;

class AutocompleteProductChain extends AbstractChain
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * AutocompleteProductChain constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function initiate()
    {
        $this->handlers = new AutocompleteSearchHandler($this->requestSender);

        return $this;
    }
}