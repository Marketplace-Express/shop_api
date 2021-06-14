<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/14
 * Time: 12:37
 */

namespace App\Application\Handlers\Product\Product;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class GetProduct extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * @var bool
     */
    private $forAdmin;

    /**
     * GetProduct constructor.
     * @param RequestSenderInterface $requestSender
     * @param bool $forAdmin
     */
    public function __construct(RequestSenderInterface $requestSender, bool $forAdmin = false)
    {
        $this->requestSender = $requestSender;
        $this->forAdmin = $forAdmin;
    }

    public function handle(array $data = [])
    {
        if (empty($data['productId'])) {
            throw new \InvalidArgumentException('product id not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        if ($this->forAdmin) {
            $route = sprintf('products/owner/%s', $data['productId']);
        } else {
            $route = sprintf('products/%s', $data['productId']);
        }

        $response = $this->requestSender->services->products->getProduct($data['productId'], $route);
        $data['product'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}