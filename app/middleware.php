<?php
declare(strict_types=1);

use App\Application\Middleware\RequestedActionMiddleware;
use RKA\Middleware\IpAddress;
use Slim\App;

return function (App $app) {
    $app->add(new IpAddress(true, ['10.0.0.1', '10.0.0.2']));
    $app->add(new RequestedActionMiddleware());

};
