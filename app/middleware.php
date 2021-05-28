<?php
declare(strict_types=1);

use App\Application\Middleware\RequestedActionMiddleware;
use RKA\Middleware\IpAddress;
use Slim\App;

/**
 * Middleware Order: First in, last executed
 * @param App $app
 * @var \Psr\Container\ContainerInterface $container
 *
 */
return function (App $app) use ($container) {
    // Throttling middleware
    $app->add($container->get('throttleMiddleware'));

    // Get requested action middleware
    $app->add(new RequestedActionMiddleware());

    // Get IP address middleware
    $app->add(new IpAddress(true, ['10.0.0.1', '10.0.0.2']));
};
