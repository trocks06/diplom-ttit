<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user || !$user->role || !in_array($user->role->role_name, $roles)) {
            return response()->json([
                'message' => 'Доступ запрещён. Ваша роль: ' . ($user->role->role_name ?? 'Не найдена')
            ], 403);
        }
        return $next($request);
    }
}
