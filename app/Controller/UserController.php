<?php

namespace ProgrammerZamanNow\MVC\Controller;

use ProgrammerZamanNow\MVC\Config\Database;
use ProgrammerZamanNow\MVC\Repository\UserRepository;
use ProgrammerZamanNow\MVC\Service\UserService;
use ProgrammerZamanNow\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\MVC\Exception\ValidationException;
use ProgrammerZamanNow\MVC\App\View;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
    }

    public function showRegisterForm()
    {
        View::render('User/register', [
            "title" => "Register new User"
        ]);
    }

    public function register()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect('/users/login');
        } catch (ValidationException $exception) {
            View::render('User/register', [
                "title" => "Register new User",
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function showLoginForm()
    {
        View::render('User/login', [
            "title" => "Login user"
        ]);
    }

    public function login()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];

        try {
            $this->userService->login($request);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/login', [
                "title" => "Login user",
                "error" => $exception->getMessage()
            ]);
        }
    }
}