<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/27
 * Time: 18:10
 */

namespace App\Application\Handlers\Product\Product;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class UpdateProduct extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * UpdateProduct constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        $productId = $data['productId'];
        unset($data['productId']);

        $response = $this->requestSender->services->products->update($productId, $data);
        $data['product'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}