<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'shop_api',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'api_throttle' => [
                'Actions\User\RegisterAction' => [
                    3600 => 2, // per hour
                    86400 => 2, // per day
                ],
                'Actions\User\LoginAction' => [
                    3600 => 5,
                    86400 => 120
                ],
                'Actions\User\GetBannedAction' => [
                    3600 => 5,
                    86400 => 120
                ],
                'Actions\User\UnBanAction' => [
                    3600 => 2,
                    86400 => 2
                ],
                'Actions\User\BanAction' => [
                    3600 => 2,
                    86400 => 2
                ],
                'Actions\Role\CreateRoleAction' => [
                    3600 => 20,
                    86400 => 50
                ],
                'Actions\Role\GetRoleAction' => [
                    3600 => 20,
                    86400 => 120
                ],
                'Actions\Role\DeleteRoleAction' => [
                    3600 => 20,
                    86400 => 120
                ],
                'Actions\Role\UpdateRoleAction' => [
                    3600 => 5,
                    86400 => 20
                ],
                'Actions\Role\AssignPermissionAction' => [
                    3600 => 30,
                    86400 => 120
                ],
                'Actions\Role\UnAssignPermissionAction' => [
                    3600 => 30,
                    86400 => 120
                ],
                'Actions\Store\CreateStoreAction' => [
                    3600 => 1,
                    86400 => 2
                ],
                'Actions\Store\DeleteStoreAction' => [
                    3600 => 1,
                    86400 => 2
                ]
            ],
            'redis_cache' => [
                'throttle' => [
                    'db' => 6
                ]
            ]
        ],
    ]);
};
