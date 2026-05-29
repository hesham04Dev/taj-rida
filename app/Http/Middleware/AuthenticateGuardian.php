<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateGuardian
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('guardian')->check()) {
            return redirect()->route('parent.login');
        }

        return $next($request);
    }
}
