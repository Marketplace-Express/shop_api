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
        $group->post('', Actions\User\User\RegisterAction::class);
        $group->post('/login', Actions\User\User\LoginAction::class);
        $group->post('/ban/{userId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', Actions\User\User\BanAction::class);
        $group->post('/unBan/{userId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', Actions\User\User\UnBanAction::class);
        $group->get('/banned', Actions\User\User\GetBannedAction::class);
        $group->get('/profile', Actions\User\User\ProfileAction::class);
    });

    $app->group('/api/roles', function (Group $group) {
        $group->get('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', Actions\User\Role\GetRoleAction::class);
        $group->post('', Actions\User\Role\CreateRoleAction::class);
        $group->delete('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', Actions\User\Role\DeleteRoleAction::class);
        $group->put('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', Actions\User\Role\UpdateRoleAction::class);
        $group->put('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/permission', Actions\User\Role\AssignPermissionAction::class);
        $group->delete('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/permission', Actions\User\Role\UnAssignPermissionAction::class);
        $group->post('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/user', Actions\User\Role\AssignRoleAction::class);
        $group->delete('/{roleId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/user', Actions\User\Role\UnAssignRoleAction::class);
    });

    $app->group('/api/stores', function (Group $group) {
        $group->post('', Actions\Store\CreateStoreAction::class);
        $group->delete('/{storeId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', Actions\Store\DeleteStoreAction::class);
        $group->post('/follow/{storeId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', Actions\Store\FollowStoreAction::class);
        $group->get('/{storeId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/followers', Actions\Store\GetFollowersAction::class);
        $group->get('/followed', Actions\Store\GetFollowedStoresAction::class);
        $group->delete('/unfollow', Actions\Store\UnFollowStoreAction::class);
    });

    $app->group('/api/categories', function (Group $group) {
        $group->post('/fetch', Actions\Category\GetCategoriesAction::class);
        $group->post('/mutate', Actions\Category\CreateCategoryAction::class);
        $group->put('/mutate', Actions\Category\UpdateCategoryAction::class);
        $group->delete('/mutate', Actions\Category\DeleteCategoryAction::class);
    });

    $app->group('/api/products', function (Group $group) {
        $group->get('/owner', Actions\Product\Product\GetProductsForAdminAction::class);
        $group->get('/{productId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', Actions\Product\Product\GetProductAction::class);
        $group->get('/owner/{productId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', Actions\Product\Product\GetProductForAdminAction::class);
        $group->get('', Actions\Product\Product\GetProductsAction::class);
        $group->post('', Actions\Product\Product\CreateProductAction::class);
        $group->put('/{productId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/quantity', Actions\Product\Product\UpdateProductQuantityAction::class);
        $group->put('/{productId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', Actions\Product\Product\UpdateProductAction::class);
        $group->delete('/{productId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', Actions\Product\Product\DeleteProductAction::class);
        $group->post('/variation', Actions\Product\Variation\CreateVariationAction::class);
        $group->put('/{variationId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/variation', Actions\Product\Variation\UpdateVariationAction::class);
        $group->delete('/{variationId:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}/variation', Actions\Product\Variation\DeleteVariationAction::class);
    });
};
