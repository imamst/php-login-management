<?php

namespace ProgrammerZamanNow\MVC\Service;

use ProgrammerZamanNow\MVC\Service\UserRegisterResponse;
use ProgrammerZamanNow\MVC\Model\UserRegisterRequest;
use ProgammerZamanNow\MVC\Exception\ValidationException;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);

            if ($user != null) {
                throw new ValidationException("User Id is already exists");
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->id, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();

            return $response;

        } catch (\Exception $exception) {
            Database::rollbackTransaction();

            throw $exception;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null || trim($request->id) == null || trim($request->name) == null || trim($request->password) == null) {
            throw new ValidationException("Id or Name or Password cannot be blank");
        }
    }
}