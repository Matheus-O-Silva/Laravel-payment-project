<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckClientPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        if (!$user->hasRole('client')) {
            abort(403, 'Unauthorized action.');
        }

        if (!$user->hasPermissions(['send_money', 'receive_money'])) {
            abort(403, 'Unauthorized action');
        }

        return $next($request);
    }
}
