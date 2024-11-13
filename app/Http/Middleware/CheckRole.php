<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle role-based authorization
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized - Please login first'], 401);
        }

        $allowedRoles = is_array($roles) ? $roles : [$roles];

        if (!in_array($request->user()->role, $allowedRoles)) {
            return response()->json([
                'message' => 'Forbidden - You do not have permission to access this resource',
                'required_roles' => $allowedRoles,
                'your_role' => $request->user()->role
            ], 403);
        }

        return $next($request);
    }
} 