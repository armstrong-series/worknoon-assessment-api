<?php

namespace App\Services;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\RegisterUserAction;

class AuthService
{

    public function __construct(
        private readonly RegisterUserAction $registerUser,
        private readonly LoginAction $authenticateUser
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
}
