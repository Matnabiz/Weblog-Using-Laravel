<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsAuthor
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && ($request->user()->role === 'author' || $request->user()->role === 'admin')) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
