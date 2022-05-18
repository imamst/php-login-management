<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ProgammerZamanNow\MVC\Config\Database;
use ProgrammerZamanNow\MVC\App\Router;
use ProgrammerZamanNow\MVC\Controller\{
    HomeController,
    UserController
};

Database::getConnection('prod');

Router::add('GET', '/', HomeController::class, 'index', []);
Router::add('GET', '/users/register', UserController::class, 'showRegisterForm', []);
Router::add('POST', '/users/register', UserController::class, 'register', []);

Router::run();
