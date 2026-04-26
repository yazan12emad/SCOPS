<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        //check if user has the required role
        if (!auth()->check() || auth()->user()->role !== $role || $request->user()->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'User is not logged in.',
            ]);
        }

        return $next($request);
    }
}
