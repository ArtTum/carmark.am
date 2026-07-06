<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = env('ADMIN_API_TOKEN', 'local-admin-token');
        $actual = (string) $request->bearerToken();

        if ($actual === '' || !hash_equals($expected, $actual)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}

