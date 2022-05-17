<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ProgrammerZamanNow\MVC\App\Router;
use ProgrammerZamanNow\MVC\Controller\{
    HomeController,
    ProductController
};
use ProgrammerZamanNow\MVC\Middleware\AuthMiddleware;

Router::add('GET', '/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)', ProductController::class, 'categories');

Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/hello', HomeController::class, 'hello', [AuthMiddleware::class]);
Router::add('GET', '/world', HomeController::class, 'world');

Router::run();
