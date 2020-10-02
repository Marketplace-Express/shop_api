<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/25
 * Time: 15:10
 */

namespace App\Services;


use App\Utilities\AbstractService;

class Users extends AbstractService
{
    const SYNC_QUEUE_NAME = 'users_sync';
    const ASYNC_QUEUE_NAME = 'users_async';

    public function register(array $data)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('user/register')
            ->setMethod('post')
            ->setBody($data)
            ->sendSync();
    }
}