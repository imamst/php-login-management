<?php

namespace ProgrammerZamanNow\MVC\App {

    // untuk mengatasi error phpunit 'cannot modify header...' saat menemui redirect dengan header('Location: //')
    function header(string $value) {
        echo $value;
    }

}

namespace ProgrammerZamanNow\MVC\Middleware {

    use PHPUnit\Framework\TestCase;
    use ProgrammerZamanNow\MVC\Config\Database;
    use ProgrammerZamanNow\MVC\Domain\User;
    use ProgrammerZamanNow\MVC\Domain\Session;
    use ProgrammerZamanNow\MVC\Middleware\MustNotLoginMiddleware;
    use ProgrammerZamanNow\MVC\Repository\UserRepository;
    use ProgrammerZamanNow\MVC\Repository\SessionRepository;
    use ProgrammerZamanNow\MVC\Service\SessionService;

    class MustNotLoginMiddlewareTest extends TestCase
    {
        private MustNotLoginMiddleware $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        public function setUp(): void
        {
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();

            $this->middleware = new MustNotLoginMiddleware();

            putenv("mode=test");
        }

        public function testBeforeGuest()
        {
            $this->middleware->before();

            $this->expectOutputString('');
        }

        public function testBeforeLoginUser()
        {
            $user = new User();
            $user->id = "imam";
            $user->name = "Imam";
            $user->password = password_hash("12345", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->middleware->before();

            $this->expectOutputRegex('[Location: /]');
        }
    }

}