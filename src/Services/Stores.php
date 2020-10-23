<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/23
 * Time: 14:55
 */

namespace App\Services;


use App\Utilities\AbstractService;

class Stores extends AbstractService
{
    const SYNC_QUEUE_NAME = 'stores_sync';
    const ASYNC_QUEUE_NAME = 'stores_async';

    public function getById($storeId)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('store/%s', $storeId))
            ->setMethod('get')
            ->sendSync();
    }
}