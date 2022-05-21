<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ProgrammerZamanNow\MVC\App\Router;
use ProgrammerZamanNow\MVC\Config\Database;
use ProgrammerZamanNow\MVC\Controller\{
    HomeController,
    UserController
};
use ProgrammerZamanNow\Middleware\MustLoginMiddleware;
use ProgrammerZamanNow\Middleware\MustNotLoginMiddleware;

Database::getConnection('prod');

Router::add('GET', '/', HomeController::class, 'index', []);
Router::add('GET', '/users/register', UserController::class, 'showRegisterForm', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', UserController::class, 'showLoginForm', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);
Router::add('GET', '/users/profile', UserController::class, 'showUpdateProfileForm', [MustLoginMiddleware::class]);
Router::add('POST', '/users/profile', UserController::class, 'updateProfile', [MustLoginMiddleware::class]);

Router::run();
