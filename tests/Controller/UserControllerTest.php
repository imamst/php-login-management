<?php

namespace ProgrammerZamanNow\MVC\App {

    // untuk mengatasi error phpunit 'cannot modify header...' saat menemui redirect dengan header('Location: //')
    function header(string $value) {
        echo $value;
    }

}

namespace ProgrammerZamanNow\MVC\Service {

    function setcookie(string $name, string $value)
    {
        echo "$name: $value";
    }
}

namespace ProgrammerZamanNow\MVC\Controller {

    use PHPUnit\Framework\TestCase;
    use ProgrammerZamanNow\MVC\Config\Database;
    use ProgrammerZamanNow\MVC\Domain\User;
    use ProgrammerZamanNow\MVC\Domain\Session;
    use ProgrammerZamanNow\MVC\Repository\UserRepository;
    use ProgrammerZamanNow\MVC\Repository\SessionRepository;
    use ProgrammerZamanNow\MVC\Service\UserService;
    use ProgrammerZamanNow\MVC\Service\SessionService;
    use ProgrammerZamanNow\MVC\Model\UserRegisterRequest;
    use ProgrammerZamanNow\MVC\Model\UserLoginRequest;
    use ProgrammerZamanNow\MVC\Exception\ValidationException;
    use ProgrammerZamanNow\MVC\Controller\UserController;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        public function setUp(): void
        {
            $this->userController = new UserController();

            $connection = Database::getConnection();
            $this->userRepository = new UserRepository($connection);
            $this->sessionRepository = new SessionRepository($connection);
            
            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testShowRegisterFormSuccess()
        {
            $this->userController->showRegisterForm();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register new User]');
        }

        public function testRegisterSuccess()
        {
            $_POST['id'] = 'imam';
            $_POST['name'] = 'Imam';
            $_POST['password'] = '12345';

            $this->userController->register();

            $this->expectOutputRegex('[Location: /users/login]');
        }

        public function testRegisterValidationError()
        {
            $_POST['id'] = '';
            $_POST['name'] = '';
            $_POST['password'] = '';

            $this->userController->register();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register new User]');
            $this->expectOutputRegex('[Id or Name or Password cannot be blank]');
        }

        public function testRegisterDuplicate()
        {
            $user = new User();
            $user->id = "imam";
            $user->name = "Imam";
            $user->password = password_hash("12345", PASSWORD_BCRYPT);

            $this->userRepository->save($user);
            
            $_POST['id'] = "imam";
            $_POST['name'] = "Imam";
            $_POST['password'] = "12345";

            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[User Id already exists]");
        }

        public function testShowLoginFormSuccess()
        {
            $this->userController->showLoginForm();

            $this->expectOutputRegex('[Login]');
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Login user]');
        }

        public function testLoginSuccess()
        {
            $user = new User();
            $user->id = "imam";
            $user->name = "Imam";
            $user->password = password_hash("12345", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'imam';
            $_POST['password'] = '12345';

            $this->userController->login();

            $this->expectOutputRegex('[Location: /]');
            $this->expectOutputRegex('[X-PZN-SESSION: ]');
        }

        public function testLoginValidationError()
        {
            $_POST['id'] = '';
            $_POST['password'] = '';

            $this->userController->login();

            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id or Password cannot be blank]');
        }

        public function testLoginNotFound()
        {
            $_POST['id'] = 'setiawan';
            $_POST['password'] = '12345';

            $this->userController->login();

            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Credentials invalid]');
        }

        public function testLoginWrongPassword()
        {
            $user = new User();
            $user->id = "imam";
            $user->name = "Imam";
            $user->password = password_hash("12345", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'imam';
            $_POST['password'] = '145';

            $this->userController->login();

            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Credentials invalid]');
        }

        public function testLogout()
        {
            $user = new User();
            $user->id = "imam";
            $user->name = "Imam";
            $user->password = password_hash("12345", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "imam";

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex('[Location: /]');
            $this->expectOutputRegex('[X-PZN-SESSION: ]');
        }

        public function testShowUpdateProfileForm()
        {
            $user = new User();
            $user->id = "imam";
            $user->name = "Imam";
            $user->password = password_hash("12345", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "imam";

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->showUpdateProfileForm();

            $this->expectOutputRegex('[Update user profile]');
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex('[imam]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Imam]');
        }

        public function testUpdateProfileSuccess()
        {
            $user = new User();
            $user->id = "imam";
            $user->name = "Imam";
            $user->password = password_hash("12345", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "imam";

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = "Imam Setiawan";
            $this->userController->updateProfile();

            $this->expectOutputRegex('[Location: /]');

            $result = $this->userRepository->findById($user->id);

            $this->assertEquals($_POST['name'], $result->name);
        }

        public function testUpdateProfileValidationError()
        {
            $user = new User();
            $user->id = "imam";
            $user->name = "Imam";
            $user->password = password_hash("12345", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "imam";

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = "";
            $this->userController->updateProfile();

            $this->expectOutputRegex('[Id or Name cannot be blank]');
            $this->expectOutputRegex('[Update user profile]');
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex('[imam]');
            $this->expectOutputRegex('[Name]');
        }
    }

}