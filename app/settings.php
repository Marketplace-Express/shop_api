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
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'api_throttle' => [
                'Actions\User\RegisterAction' => [
                    3600 => 1, // per hour
                    86400 => 2, // per day
                ],
                'Actions\User\LoginAction' => [
                    3600 => 5,
                    86400 => 120
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
