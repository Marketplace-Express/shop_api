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

    public function getProduct(string $id)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('products/%s', $id))
            ->setMethod('get')
            ->sendSync();
    }
}