<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(HttpRequest $request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } elseif (config('app.locale')) {
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
