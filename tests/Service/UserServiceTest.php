<?php

namespace ProgrammerZamanNow\MVC\Service;

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\MVC\Config\Database;
use ProgrammerZamanNow\MVC\Domain\User;
use ProgrammerZamanNow\MVC\Repository\UserRepository;
use ProgrammerZamanNow\MVC\Service\UserService;
use ProgrammerZamanNow\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\MVC\Exception\ValidationException;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);

        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = 1;
        $request->name = "Imam";
        $request->password = "12345";

        $response = $this->userService->register($request);

        $this->assertEquals($request->id, $response->user->id);
        $this->assertEquals($request->name, $response->user->name);
        $this->assertNotEquals($request->password, $response->user->password);

        $this->assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = 0;
        $request->name = "";
        $request->password = "";

        $this->userService->register($request);
    }

    public function testRegisterDuplicate()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = 1;
        $user->name = "Imam";
        $user->password = "12345";

        $this->userRepository->save($user);

        $request = new UserRegisterRequest();
        $request->id = 1;
        $request->name = "Imam";
        $request->password = "12345";

        $this->userService->register($request);
    }
}