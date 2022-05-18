<?php

namespace ProgarmmerZamanNow\MVC\Controller;

use ProgammerZamanNow\MVC\Config\Database;
use ProgammerZamanNow\MVC\Repository\UserRepository;
use ProgammerZamanNow\MVC\Service\UserService;
use ProgammerZamanNow\MVC\App\View;

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
        $request->id = null;
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
}