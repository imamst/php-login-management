<?php

namespace ProgrammerZamanNow\MVC\App {
    
    // untuk mengatasi error phpunit 'cannot modify header...' saat menemui redirect dengan header('Location: //')
    function header(string $value) {
        echo $value;
    }

}

namespace ProgrammerZamanNow\MVC\Controller {

    use PHPUnit\Framework\TestCase;
    use ProgrammerZamanNow\MVC\Config\Database;
    use ProgrammerZamanNow\MVC\Domain\User;
    use ProgrammerZamanNow\MVC\Repository\UserRepository;
    use ProgrammerZamanNow\MVC\Service\UserService;
    use ProgrammerZamanNow\MVC\Model\UserRegisterRequest;
    use ProgrammerZamanNow\MVC\Exception\ValidationException;
    use ProgrammerZamanNow\MVC\Controller\UserController;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;

        public function setUp(): void
        {
            $this->userController = new UserController();

            $connection = Database::getConnection();
            $this->userRepository = new UserRepository($connection);
            
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testShowRegisterFormSuccess()
        {
            $this->userController->showRegisterForm();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register new User]');
        }

        public function testRegisterSuccess()
        {
            $_POST['name'] = 'Imam';
            $_POST['password'] = '12345';

            $this->userController->register();

            $this->expectOutputRegex('[Location: /users/login]');
        }

        public function testRegisterValidationError()
        {
            $_POST['name'] = '';
            $_POST['password'] = '12345';

            $this->userController->register();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register new User]');
            $this->expectOutputRegex('[Name or Password cannot be blank]');
        }

        // public function testRegisterDuplicate()
        // {
            
        // }
    }

}