<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/22
 * Time: 00:17
 */

namespace App\Application\Handlers\Product\Product;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class GetProducts extends AbstractHandler
{
    const DEFAULT_LIMIT = 10;

    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * GetProducts constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? self::DEFAULT_LIMIT;
        $sort = $data['sort'] ?? null;
        $storeId = $data['storeId'] ?? null;
        $categoryId = $data['categoryId'] ?? null;

        $response = $this->requestSender->services->products->getProducts($storeId, $categoryId, $page, $limit, $sort);
        $data['products'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}