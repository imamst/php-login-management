<?php

namespace ProgrammerZamanNow\MVC\Middleware;

use ProgrammerZamanNow\MVC\App\View;
use ProgrammerZamanNow\MVC\Config\Database;
use ProgrammerZamanNow\MVC\Middleware\Middleware;
use ProgrammerZamanNow\MVC\Repository\UserRepository;
use ProgrammerZamanNow\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\MVC\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function before(): void
    {
        $user = $this->sessionService->current();

        if ($user != null) {
            View::redirect('/');
        }
    }
}