<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        // Skip verifikasi untuk admin
        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        // Cek verifikasi untuk role lainnya
        if (!$user || !$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email belum diverifikasi.',
                'verification_required' => true
            ], 403);
        }

        return $next($request);
    }
} 