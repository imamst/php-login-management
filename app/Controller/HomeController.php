<?php

namespace ProgrammerZamanNow\MVC\Controller;

use ProgrammerZamanNow\MVC\Config\Database;
use ProgrammerZamanNow\MVC\Repository\UserRepository;
use ProgrammerZamanNow\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\MVC\Service\SessionService;
use ProgrammerZamanNow\MVC\App\View;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function index(): void
    {
        $user = $this->sessionService->current();

        if ($user == null) {
            View::render("Home/index", [
                'title' => 'PHP Login Management'
            ]);
        } else {
            View::render("Home/dashboard", [
                'title' => 'Dashboard',
                'user' => [
                    'name' => $user->name
                ]
            ]);
        }
    }
}