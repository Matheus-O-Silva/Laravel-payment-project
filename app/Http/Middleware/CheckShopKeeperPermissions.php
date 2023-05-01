<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckShopKeeperPermissions
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

        if (!$user->hasRole('shopkeeper')) {
            abort(403, 'Unauthorized action.');
        }

        if (!$user->hasPermissions(['receive_money'])) {
            abort(403, 'Unauthorized action');
        }

        return $next($request);
    }
}
