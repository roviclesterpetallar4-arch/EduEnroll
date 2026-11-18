<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        abort_unless($role === 'admin' && $request->user()?->isAdmin(), 403);

        return $next($request);
    }
}
