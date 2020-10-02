<?php
declare(strict_types=1);


use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            'container' => function (ContainerInterface $container) {
                return $container;
            },
            Psr\Log\LoggerInterface::class => function (ContainerInterface $container) {
                $settings = $container->get('settings');

                $loggerSettings = $settings['logger'];
                $logger = new Monolog\Logger($loggerSettings['name']);

                $processor = new Monolog\Processor\UidProcessor();
                $logger->pushProcessor($processor);

                $handler = new Monolog\Handler\StreamHandler($loggerSettings['path'], $loggerSettings['level']);
                $logger->pushHandler($handler);

                return $logger;
            },
            App\Utilities\RequestSenderInterface::class => function () {
                return new App\Utilities\RequestSender(
                    new App\Utilities\AmqpHandler(
                        $_ENV['RABBITMQ_HOST'],
                        $_ENV['RABBITMQ_PORT'],
                        $_ENV['RABBITMQ_USER'],
                        $_ENV['RABBITMQ_PASS']
                    ), [
                        App\Services\Categories::class,
                        App\Services\Products::class,
                        App\Services\Users::class
                    ]
                );
            },
            'redisConnector' => function (ContainerInterface $container) {
                return (new App\Application\Utilities\Connector())->connect(
                    $_ENV['REDIS_HOST'],
                    $_ENV['REDIS_PORT'],
                    $container->get('settings')['redis_cache']['throttle']['db'],
                    $_ENV['REDIS_PASSWORD']
                );
            },
        ],
        [
            'throttleService' => DI\autowire(App\Application\Services\ThrottleService::class)
                ->constructorParameter('connector', DI\get('redisConnector'))
                ->constructorParameter('container', DI\get('container'))
        ],
        [
            'throttleMiddleware' => DI\autowire(App\Application\Middleware\ThrottleMiddleware::class)
                ->constructorParameter('service', DI\get('throttleService'))
        ]
    );
};
