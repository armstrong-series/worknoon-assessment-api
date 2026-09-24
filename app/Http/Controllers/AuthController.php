<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{

    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function authenticate(LoginRequest $request)
    {
        $email = $request->string('email')->toString();
        $password = $request->string('password')->toString();
        $result = $this->authService->authenticate($email, $password);

        return worknoonResponse(
            $result,
            200,
            'Authenticated',
            true,
            app('url')->current(),
            [],
            'auth'
        );
    }


    public function signup(RegisterRequest $request)
    {
        $result = $this->authService->signup($request->validatedAttributes());

        return worknoonResponse(
            $result,
            201,
            'Signup successful',
            true,
            app('url')->current(),
            [],
            'auth'
        );
    }
}
