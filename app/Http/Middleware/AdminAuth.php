<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if the user is an admin
        if (!Auth::user()->admin) {
            // If not an admin, abort and return
            abort(403, 'Unauthorized');
        }

        // If the user is an admin, allow the request to proceed
        return $next($request);
    }
}
