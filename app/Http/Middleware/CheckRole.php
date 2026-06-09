<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        $allowedRoles = array_map(
            fn (string $role): UserRole => UserRole::from($role),
            $roles,
        );

        if (! in_array($user->role, $allowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
