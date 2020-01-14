<?php

use Middlewares\Whoops;
use App\Blog\BlogModule;
use App\Admin\AdminModule;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\RouterMiddleware;
use Framework\Middleware\CsrfMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;

chdir(dirname(__DIR__));
// require autoload composer
require 'vendor/autoload.php';

$modules = [
    AdminModule::class,
    BlogModule::class
];
$app = (new \Framework\App('config/config.php'))
    ->addModule(AdminModule::class)
    ->addModule(BlogModule::class)
    ->pipe(Whoops::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(MethodMiddleware::class)
    ->pipe(CsrfMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class);
if (php_sapi_name() !== "cli") {
    $response = $app->run(ServerRequest::fromGlobals());
    \Http\Response\send($response);
}
