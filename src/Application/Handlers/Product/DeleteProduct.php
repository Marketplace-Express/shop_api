<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/27
 * Time: 18:21
 */

namespace App\Application\Handlers\Product;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class DeleteProduct extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * DeleteProduct constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        $this->requestSender->services->products->delete($data['productId']);

        return parent::handle($data);
    }
}