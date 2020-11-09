<?php
declare(strict_types=1);

use App\Application\Actions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;


return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->group('/api/users', function (Group $group) {
        $group->post('', Actions\User\RegisterAction::class);

        $group->post('/login', Actions\User\LoginAction::class);

        $group->post(
            '/ban/{userId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}',
            Actions\User\BanAction::class);

        $group->post('/unBan/{userId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}',
            Actions\User\UnBanAction::class);

        $group->get('/banned', Actions\User\GetBannedAction::class);
    });

    $app->group('/api/roles', function (Group $group) {
        $group->get('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}',
            Actions\Role\GetRoleAction::class);

        $group->post('', Actions\Role\CreateRoleAction::class);

        $group->delete('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}',
            Actions\Role\DeleteRoleAction::class);

        $group->put('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}',
            Actions\Role\UpdateRoleAction::class);

        $group->put('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/permission',
            Actions\Role\AssignPermissionAction::class);

        $group->delete('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/permission',
            Actions\Role\UnAssignPermissionAction::class);

        $group->post('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/user',
            Actions\Role\AssignRoleAction::class);

        $group->delete('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/user',
            Actions\Role\UnAssignRoleAction::class);
    });

    $app->group('/api/stores', function (Group $group) {
        $group->post('', Actions\Store\CreateStoreAction::class);

        $group->delete('/{storeId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}',
            Actions\Store\DeleteStoreAction::class);

        $group->post('/follow/{storeId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}',
            Actions\Store\FollowStoreAction::class);

        $group->get('/{storeId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/followers',
            Actions\Store\GetFollowersAction::class);

        $group->get('/followed', Actions\Store\GetFollowedStoresAction::class);

        $group->delete('/unfollow', Actions\Store\UnFollowStoreAction::class);
    });

    $app->group('/api/categories', function (Group $group) {
        $group->post('/fetch', Actions\Category\GetCategoriesAction::class);
        $group->post('/mutate', Actions\Category\CreateCategoryAction::class);
    });
};
