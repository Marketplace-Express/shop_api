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
        $group->post('', Actions\User\RegisterAction::class);

        $group->post('/login', Actions\User\LoginAction::class);

        $group->post(
            '/ban/{userId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}',
            Actions\User\BanAction::class
        )->addMiddleware($container->get('authorizeMiddleware'));

        $group->post('/unBan/{userId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}',
            Actions\User\UnBanAction::class
        )->addMiddleware($container->get('authorizeMiddleware'));

        $group->get('/banned', Actions\User\GetBannedAction::class)
            ->addMiddleware($container->get('authorizeMiddleware'));
    });

    $app->group('/api/roles', function (Group $group) use ($container) {
        $group->get('/{roleId}', Actions\Role\GetRoleAction::class);
        $group->post('', Actions\Role\CreateRoleAction::class);
    });
};
