<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/12/04
 * Time: 15:24
 */

namespace App\Application\Handlers\Product\Product;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class UpdateProductQuantity extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * UpdateQuantity constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    /**
     * @param array $data
     * @return array|mixed
     */
    public function handle(array $data = [])
    {
        if (!isset($data['productId'])) {
            throw new \InvalidArgumentException('product is is not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $productId = $data['productId'];
        unset($data['productId']);

        $this->requestSender->services->products->updateQuantity($productId, $data);

        return parent::handle($data);
    }
}