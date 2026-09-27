<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;

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
            'auth',
            [
                $result['user'],
                $result['user']->role,
                $result['user']->customer,
            ],
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


    public function me(Request $request)
    {
        $result = $this->authService->getAuthenticatedUser(
            $request->user()
        );

        return worknoonResponse(
            $result,
            200,
            'Authenticated user fetched successfully.',
            true,
            app('url')->current(),
            [],
            'users',
            [
                $result,
                $result->role,
                $result->customer,
            ],
        );
    }
}
