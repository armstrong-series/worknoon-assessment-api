<?php

namespace App\Services;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\RegisterUserAction;
use App\Actions\Auth\GetAuthenticatedUserAction;
use App\Models\User;

class AuthService
{

    public function __construct(
        private readonly RegisterUserAction $registerUser,
        private readonly LoginAction $authenticateUser,
        private readonly GetAuthenticatedUserAction $getAuthenticatedUser
    ) {}



    public function signup(array $attributes): array
    {
        $user = $this->registerUser->execute($attributes);

        return [
            'user'       => $user,
            'token'      =>  $user->createToken('api')->plainTextToken,
            'token_type' => 'bearer',

        ];
    }

    public function authenticate(string $email, string $password): array
    {
        $user = $this->authenticateUser->execute($email, $password);

        return [
            'user'       => $user,
            'token'      => $user->createToken('api')->plainTextToken,
            'token_type' => 'bearer',
        ];
    }

    public function getAuthenticatedUser(User $user): User
    {
        return $this->getAuthenticatedUser->execute($user);
    }
}
