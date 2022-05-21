<?php

namespace ProgrammerZamanNow\MVC\Service;

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\MVC\Config\Database;
use ProgrammerZamanNow\MVC\Domain\User;
use ProgrammerZamanNow\MVC\Exception\ValidationException;
use ProgrammerZamanNow\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\MVC\Model\UserUpdateProfileRequest;
use ProgrammerZamanNow\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\MVC\Repository\UserRepository;
use ProgrammerZamanNow\MVC\Service\UserService;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    public function setUp(): void
    {
        $connection = Database::getConnection();

        $this->sessionRepository = new SessionRepository($connection);
        $this->sessionRepository->deleteAll();

        $this->userRepository = new UserRepository($connection);
        $this->userRepository->deleteAll();

        $this->userService = new UserService($this->userRepository);
    }

    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = "imam";
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
        $request->id = "";
        $request->name = "";
        $request->password = "";

        $this->userService->register($request);
    }

    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = "imam";
        $user->name = "Imam";
        $user->password = "12345";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "imam";
        $request->name = "Imam";
        $request->password = "12345";

        $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "imam";
        $request->password = "12345";

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "imam";
        $user->name = "imam";
        $user->password = password_hash("12345", PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "imam";
        $request->password = "345";

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "imam";
        $user->name = "imam";
        $user->password = password_hash("12345", PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $request = new UserLoginRequest();
        $request->id = "imam";
        $request->password = "12345";

        $response = $this->userService->login($request);

        $this->assertEquals($request->id, $response->user->id);
        $this->assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = "imam";
        $user->name = "Imam";
        $user->password = password_hash("12345", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserUpdateProfileRequest();
        $request->id = "imam";
        $request->name = "Imam Setiawan";
        
        $response = $this->userService->updateProfile($request);

        $user = $this->userRepository->findById($request->id);

        $this->assertEquals($request->name, $user->name);
    }

    public function testUpdateValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserUpdateProfileRequest();
        $request->id = "";
        $request->name = "";
        
        $this->userService->updateProfile($request);
    }

    public function testUpdateUserNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserUpdateProfileRequest();
        $request->id = "imams";
        $request->name = "Imam Setiawan";
        
        $this->userService->updateProfile($request);
    }
}