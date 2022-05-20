<?php

namespace ProgrammerZamanNow\Middleware;

use ProgrammerZamanNow\App\View;
use ProgrammerZamanNow\Config\Database;
use ProgrammerZamanNow\Middleware\Middleware;
use ProgrammerZamanNow\Repository\UserRepository;
use ProgrammerZamanNow\Repository\SessionRepository;
use ProgrammerZamanNow\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function before()
    {
        $user = $this->sessionService->current();

        if ($user != null) {
            View::render('/');
        }
    }
}