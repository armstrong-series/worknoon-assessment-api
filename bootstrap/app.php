<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

if (! function_exists('mapRoutes')) {
    function mapRoutes(): void
    {
        $routes = [
            'auth'    => '/../routes/auth/auth.php',
            'refunds' => '/../routes/refunds/refunds.php',
        ];

        foreach ($routes as $prefix => $routeFile) {
            Route::prefix($prefix)->group(function () use ($routeFile): void {
                require __DIR__ . $routeFile;
            });
        }
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('api')
                ->group(function (): void {
                    mapRoutes();
                });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('api', HandleCors::class);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return '/login';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->shouldRenderJsonWhen(
            fn(Request $request): bool =>
            $request->expectsJson()
                || $request->is('api/*'),
        );

        $exceptions->render(function (
            AuthenticationException $e
        ): JsonResponse {
            return worknoonResponse(
                [],
                401,
                $e->getMessage() ?: 'Unauthenticated.',
                false,
                request()->fullUrl(),
            );
        });

        $exceptions->render(function (
            AccessDeniedHttpException $e
        ): JsonResponse {
            return worknoonResponse(
                [],
                403,
                $e->getMessage() ?: 'This action is unauthorized.',
                false,
                request()->fullUrl(),
            );
        });
    })
    ->create();
