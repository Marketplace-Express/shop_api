<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/06
 * Time: 13:58
 */

namespace App\Application\Chains\Category;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\Category\GetCategories;
use App\Application\Handlers\Store\GetStore;
use App\Utilities\RequestSenderInterface;

class GetCategoriesChain extends AbstractChain
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * GetCategoriesChain constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function initiate()
    {
        $handlers = new GetStore($this->requestSender);
        $handlers
            ->next(new GetCategories($this->requestSender));

        $this->handlers = $handlers;

        return $this;
    }
}