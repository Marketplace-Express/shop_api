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

    public function register(
        $firstName,
        $lastName,
        $gender,
        $birthdate,
        $password,
        $email
    ) {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('user/register')
            ->setMethod('post')
            ->setBody([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'gender' => $gender,
                'birthdate' => $birthdate,
                'password' => $password,
                'email' => $email
            ])
            ->sendSync();
    }

    public function login($userName, $password)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('user/login')
            ->setMethod('post')
            ->setBody(['user_name' => $userName, 'password' => $password])
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
        array $user,
        array $headers,
        array $permissions,
        string $operator,
        string $policyModel,
        array $authorizeData = []
    ) {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('auth/authorized')
            ->setMethod('post')
            ->setHeaders($headers)
            ->setBody([
                'user' => $user,
                'permissions' => $permissions,
                'operator' => $operator,
                'policyModel' => $policyModel,
                'authorizeData' => $authorizeData
            ])->sendSync();
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

    public function deleteRole(string $roleId)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('role/%s', $roleId))
            ->setMethod('delete')
            ->sendSync();
    }

    public function updateRole(string $roleId, $roleName)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('role/%s', $roleId))
            ->setMethod('put')
            ->setBody(['role_name' => $roleName])
            ->sendSync();
    }

    public function assignPermission($roleId, $permission)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('role/%s/permission', $roleId))
            ->setMethod('put')
            ->setBody(['permission' => $permission])
            ->sendSync();
    }

    public function unAssignPermission($roleId, $permission)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('role/%s/permission', $roleId))
            ->setMethod('delete')
            ->setBody(['permission' => $permission])
            ->sendSync();
    }

    public function assignRole(string $roleId, $userId)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('role/%s/user', $roleId))
            ->setMethod('post')
            ->setBody(['user_id' => $userId])
            ->sendSync();
    }

    public function unAssignRole(string $roleId, $userId)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute(sprintf('role/%s/user', $roleId))
            ->setMethod('delete')
            ->setBody(['user_id' => $userId])
            ->sendSync();
    }

    public function getByIds(array $usersIds)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE_NAME)
            ->setRoute('user/all')
            ->setMethod('post')
            ->setBody(['usersIds' => $usersIds])
            ->sendSync();
    }
}