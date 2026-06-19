<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user || ! in_array((int) $user->role_id, [1, 2], true)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}