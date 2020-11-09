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

    public function isStoreOwner($userId, $storeId)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('store/%s/isOwner', $storeId))
            ->setMethod('get')
            ->setBody(['user_id' => $userId])
            ->sendSync();
    }

    public function create(
        $ownerId,
        $name,
        $description,
        $type,
        $location,
        $photo,
        $coverPhoto
    ) {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('store/')
            ->setMethod('post')
            ->setBody([
                'ownerId' => $ownerId,
                'name' => $name,
                'description' => $description,
                'type' => $type,
                'location' => $location,
                'photo' => $photo,
                'coverPhoto' => $coverPhoto
            ])
            ->sendSync();
    }

    public function delete(string $storeId)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('store/%s', $storeId))
            ->setMethod('delete')
            ->sendSync();
    }

    public function follow(string $storeId, $followerId)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('follow/%s', $storeId))
            ->setMethod('post')
            ->setBody(['followerId' => $followerId])
            ->sendSync();
    }

    public function unfollow(string $userId, $storeId)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('follow/unfollow')
            ->setMethod('delete')
            ->setBody(['storeId' => $storeId, 'followerId' => $userId])
            ->sendSync();
    }

    public function getFollowers(string $storeId, $page, $limit)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('follow/%s/followers', $storeId))
            ->setMethod('get')
            ->setQuery(['page' => $page, 'limit' => $limit])
            ->sendSync();
    }

    public function getFollowed(string $userId, $page, $limit)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('follow/%s/followed', $userId))
            ->setMethod('get')
            ->setQuery(['page' => $page, 'limit' => $limit])
            ->sendSync();
    }
}