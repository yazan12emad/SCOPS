<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAuthenticatedLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() || auth('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User is already logged in.',
            ], 409);
        }

        return $next($request);
    }
}
