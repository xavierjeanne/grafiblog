<?php

use Middlewares\Whoops;
use App\Blog\BlogModule;
use App\Admin\AdminModule;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\RouterMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;

// require autoload composer
require dirname(__DIR__) . '/vendor/autoload.php';

$modules = [
    AdminModule::class,
    BlogModule::class
];
$app = (new \Framework\App(dirname(__DIR__) . '/config/config.php'))
    ->addModule(AdminModule::class)
    ->addModule(BlogModule::class)
    ->pipe(Whoops::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(MethodMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class);
if (php_sapi_name() !== "cli") {
    $response = $app->run(ServerRequest::fromGlobals());
    \Http\Response\send($response);
}
