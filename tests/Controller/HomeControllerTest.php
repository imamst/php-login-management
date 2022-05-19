<?php

namespace ProgrammerZamanNow\MVC\Controller;

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\MVC\Config\Database;
use ProgrammerZamanNow\MVC\Domain\User;
use ProgrammerZamanNow\MVC\Domain\Session;
use ProgrammerZamanNow\MVC\Repository\UserRepository;
use ProgrammerZamanNow\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\MVC\Service\SessionService;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest()
    {
        $this->homeController->index();

        $this->expectOutputRegex('[Login Management]');
    }

    public function testUser()
    {
        $user = new User();
        $user->id = "imam";
        $user->name = "Imam";
        $user->password = "12345";

        $this->userRepository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();

        $this->expectOutputRegex('[Hello Imam]');
    }
}