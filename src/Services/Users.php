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

    public function login(array $data)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('user/login')
            ->setMethod('post')
            ->setHeaders(['csrf-token' => $data['csrf-token']])
            ->setBody($data)
            ->sendSync();
    }

    public function isAuthenticated(string $token, array $headers)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('auth/authenticated')
            ->setHeaders($headers)
            ->setMethod('post')
            ->setBody(['token' => $token])
            ->sendSync();
    }

    public function isAuthorized(
        string $token,
        array $headers,
        array $permissions,
        string $operator,
        string $policyModel,
        array $authorizeData = []
    )
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('auth/authorized')
            ->setMethod('post')
            ->setHeaders($headers)
            ->setBody(
                [
                    'token' => $token,
                    'permissions' => $permissions,
                    'operator' => $operator,
                    'policyModel' => $policyModel,
                    'authorizeData' => $authorizeData
                ]
            )->sendSync();
    }

    public function ban(string $userId, $reason, $description)
    {
        $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setMethod('post')
            ->setRoute(sprintf('user/ban/%s', $userId))
            ->setBody(['reason' => $reason, 'description' => $description])
            ->sendSync();
    }

    public function unBan(string $userId)
    {
        $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('user/unBan/%s', $userId))
            ->setMethod('post')
            ->sendSync();
    }

    public function getBanned($page, $limit)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('users/banned')
            ->setMethod('get')
            ->setQuery(['page' => (int) $page, 'limit' => (int) $limit])
            ->sendSync();
    }

    public function getRole(string $roleId)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('role/%s', $roleId))
            ->setMethod('get')
            ->sendSync();
    }

    public function createRole(string $roleName, string $storeId)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('role/create')
            ->setMethod('post')
            ->setBody(['role_name' => $roleName, 'store_id' => $storeId])
            ->sendSync();
    }
}