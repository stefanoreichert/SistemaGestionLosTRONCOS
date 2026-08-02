<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user !== null, 403, 'No tiene permiso para acceder a esta sección.');
        abort_unless(in_array($user->role, $roles, true), 403, 'No tiene permiso para acceder a esta sección.');

        return $next($request);
    }
}
