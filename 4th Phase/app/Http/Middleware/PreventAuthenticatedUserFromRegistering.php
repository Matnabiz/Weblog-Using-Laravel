<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreventAuthenticatedUserFromRegistering
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('api')->check()) {
            return response()->json(['error' => 'You are already logged in and cannot register again.'], 403);
        }

        return $next($request);
    }
}

