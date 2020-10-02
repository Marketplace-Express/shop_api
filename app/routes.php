<?php
declare(strict_types=1);

use App\Application\Actions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

global $container;

return function (App $app) use ($container) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->group('/api/users', function (Group $group) use ($container) {
        $group->post('', Actions\User\RegisterAction::class)
            ->addMiddleware($container->get('throttleMiddleware'));
    });
};
