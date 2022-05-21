<?php

namespace ProgrammerZamanNow\MVC\Service;

use ProgrammerZamanNow\MVC\Config\Database;
use ProgrammerZamanNow\MVC\Domain\User;
use ProgrammerZamanNow\MVC\Exception\ValidationException;
use ProgrammerZamanNow\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\MVC\Model\UserLoginResponse;
use ProgrammerZamanNow\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\MVC\Model\UserRegisterResponse;
use ProgrammerZamanNow\MVC\Model\UserUpdatePasswordRequest;
use ProgrammerZamanNow\MVC\Model\UserUpdatePasswordResponse;
use ProgrammerZamanNow\MVC\Model\UserUpdateProfileRequest;
use ProgrammerZamanNow\MVC\Model\UserUpdateProfileResponse;
use ProgrammerZamanNow\MVC\Repository\UserRepository;

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
                throw new ValidationException("User Id already exists");
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

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

    public function updateProfile(UserUpdateProfileRequest $request): UserUpdateProfileResponse
    {
        $this->validateUserUpdateProfileRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("User is not found");
            }

            $user->name = $request->name;
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserUpdateProfileResponse();
            $response->user = $user;
            return $response;

        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException("Credentials invalid");
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;

            return $response;
        } else {
            throw new ValidationException("Credentials invalid");
        }
    }

    public function updatePassword(
                                    UserUpdatePasswordRequest $request
                                ): UserUpdatePasswordResponse
    {
        $this->validateUserUpdatePasswordRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("User is not found");
            }

            if (!password_verify($request->oldPassword, $user->password)) {
                throw new ValidationException("Old password incorrect");
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserUpdatePasswordResponse();
            $response->user = $user;
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->name == null || $request->password == null || trim($request->name) == null || trim($request->password) == null) {
            throw new ValidationException("Id or Name or Password cannot be blank");
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null || trim($request->id) == null || trim($request->password) == null) {
            throw new ValidationException("Id or Password cannot be blank");
        }
    }

    private function validateUserUpdateProfileRequest(UserUpdateProfileRequest $request)
    {
        if ($request->id == null || $request->name == null || trim($request->id) == null || trim($request->name) == null) {
            throw new ValidationException("Id or Name cannot be blank");
        }
    }

    private function validateUserUpdatePasswordRequest(UserUpdatePasswordRequest $request)
    {
        if ($request->id == null || $request->oldPassword == null || $request->newPassword == null || trim($request->id) == null || trim($request->oldPassword) == null || ($request->newPassword) == null) {
            throw new ValidationException("Id, old password and new password cannot be blank");
        }
    }
}