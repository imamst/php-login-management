<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ProgrammerZamanNow\MVC\App\Router;
use ProgrammerZamanNow\MVC\Controller\{
    HomeController
};

Router::add('GET', '/', HomeController::class, 'index', []);

Router::run();
