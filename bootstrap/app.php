<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;          // <-- добавить
use Illuminate\Http\Response;         // <-- добавить
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Требуется авторизация. Пожалуйста, войдите в систему.',
                ], Response::HTTP_UNAUTHORIZED);
            }
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'У вас недостаточно прав для выполнения этого действия.',
                ], Response::HTTP_FORBIDDEN);
            }
        });

        $exceptions->render(function (NotFoundHttpException|ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                $message = 'Ресурс не найден.';
                if ($e instanceof ModelNotFoundException) {
                    $model = class_basename($e->getModel());
                    $message = "{$model} не найден(а).";
                }
                return response()->json([
                    'message' => $message,
                ], Response::HTTP_NOT_FOUND);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Метод запроса не поддерживается для этого маршрута.',
                    'allowed_methods' => $e->getHeaders()['Allow'] ?? null,
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Переданы некорректные данные.',
                    'errors' => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? null;
                return response()->json([
                    'message' => 'Слишком много запросов. Попробуйте позже.',
                    'retry_after_seconds' => $retryAfter,
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }
        });
    })->create();
