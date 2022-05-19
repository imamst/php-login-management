<?php

namespace ProgrammerZamanNow\MVC\Repository;

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\MVC\Config\Database;
use ProgrammerZamanNow\MVC\Domain\User;
use ProgrammerZamanNow\MVC\Domain\Session;
use ProgrammerZamanNow\MVC\Repository\UserRepository;
use ProgrammerZamanNow\MVC\Repository\SessionRepository;

class SessionRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    public function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->userRepository->deleteAll();
        $this->sessionRepository->deleteAll();

        $user = new User();
        $user->id = "imam";
        $user->name = "Imam";
        $user->password = password_hash("12345", PASSWORD_BCRYPT);

        $this->userRepository->save($user);
    }

    public function testSaveSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "imam";

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->id);

        $this->assertEquals($session->id, $result->id);
        $this->assertEquals($session->userId, $result->userId);
    }

    public function testDeleteByIdSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "imam";

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->id);

        $this->assertEquals($session->id, $result->id);
        $this->assertEquals($session->userId, $result->userId);

        $this->sessionRepository->deleteById($session->id);

        $result = $this->sessionRepository->findById($session->id);

        $this->assertNull($result);
    }

    public function testFindByIdNotFound()
    {
        $result = $this->sessionRepository->findById(100);

        $this->assertNull($result);
    }
}