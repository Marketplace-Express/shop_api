<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/19
 * Time: 13:51
 */

namespace App\Services;


use App\Utilities\AbstractService;
use App\Utilities\ServiceInterface;

/**
 * Class Products
 * @package App\Utilities\Services\Products
 */
class Products extends AbstractService implements ServiceInterface
{
    const SYNC_QUEUE_NAME = 'products_sync';
    const ASYNC_QUEUE_NAME = 'products_async';

    public function getProduct(string $id, string $route)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute($route)
            ->setMethod('get')
            ->sendSync();
    }

    public function getProducts($storeId, $categoryId, $page, $limit, $sort)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('products')
            ->setMethod('get')
            ->setQuery([
                'storeId' => $storeId,
                'categoryId' => $categoryId,
                'page' => $page,
                'limit' => $limit,
                'sort' => $sort
            ])
            ->sendSync();
    }

    public function getProductsForAdmin($storeId, $categoryId, $page, $limit, $sort)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('products/owner')
            ->setMethod('get')
            ->setQuery([
                'storeId' => $storeId,
                'categoryId' => $categoryId,
                'page' => $page,
                'limit' => $limit,
                'sort' => $sort
            ])
            ->sendSync();
    }

    public function create(array $data)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('products')
            ->setMethod('post')
            ->setBody($data)
            ->sendSync();
    }

    public function update(string $productId, array $data)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('products/%s', $productId))
            ->setMethod('put')
            ->setBody($data)
            ->sendSync();
    }

    public function delete(string $productId)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('products/%s', $productId))
            ->setMethod('delete')
            ->sendSync();
    }
}