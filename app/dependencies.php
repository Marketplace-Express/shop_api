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
                        App\Services\Users::class,
                        App\Services\Stores::class
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
            'tokenAuth' => function () {
                return new Slim\Middleware\TokenAuthentication();
            }
        ],
        [
            'throttleService' => DI\autowire(App\Application\Services\ThrottleService::class)
                ->constructorParameter('connector', DI\get('redisConnector'))
                ->constructorParameter('container', DI\get('container')),

            'authService' => DI\autowire(App\Application\Services\AuthService::class)
                ->constructorParameter('requestSender', DI\get(App\Utilities\RequestSenderInterface::class))
        ],
        [
            'throttleMiddleware' => DI\autowire(App\Application\Middleware\ThrottleMiddleware::class)
                ->constructorParameter('service', DI\get('throttleService')),

            'authenticateMiddleware' => DI\autowire(App\Application\Middleware\AuthenticateMiddleware::class)
                ->constructorParameter('service', DI\get('authService'))
                ->constructorParameter('tokenAuthentication', DI\get('tokenAuth')),

            'authorizeMiddleware' => DI\autowire(App\Application\Middleware\AuthorizeMiddleware::class)
                ->constructorParameter('service', DI\get('authService'))
                ->constructorParameter('tokenAuthentication', DI\get('tokenAuth')),
        ]
    );
};
