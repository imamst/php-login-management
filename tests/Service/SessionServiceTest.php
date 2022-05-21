<?php

namespace ProgrammerZamanNow\MVC\Service;

// handle setcookie (header manipulation)
require_once __DIR__ . '/../Helper/helper.php';

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\MVC\Config\Database;
use ProgrammerZamanNow\MVC\Domain\User;
use ProgrammerZamanNow\MVC\Domain\Session;
use ProgrammerZamanNow\MVC\Repository\UserRepository;
use ProgrammerZamanNow\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\MVC\Service\SessionService;

class SessionServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    private SessionService $sessionService;
    private string $cookie_name;

    public function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);
        $this->cookie_name = SessionService::$COOKIE_NAME;

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "imam";
        $user->name = "Imam";
        $user->password = password_hash("12345", PASSWORD_BCRYPT);

        $this->userRepository->save($user);
    }

    public function testCreate()
    {
        $session = $this->sessionService->create("imam");

        $this->expectOutputRegex("[$this->cookie_name: $session->id]");

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals("imam", $result->userId);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "imam";

        $this->sessionRepository->save($session);
        
        $_COOKIE[$this->cookie_name] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[$this->cookie_name: ]");

        $result = $this->sessionRepository->findById($session->id);
        $this->assertNull($result);
    }

    public function testCurrentSessionSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "imam";

        $this->sessionRepository->save($session);
        
        $_COOKIE[$this->cookie_name] = $session->id;

        $user = $this->sessionService->current();

        $this->assertEquals($session->userId, $user->id);
    }
}