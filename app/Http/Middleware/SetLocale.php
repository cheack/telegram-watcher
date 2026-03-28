<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale'));

        if (array_key_exists($locale, config('app.available_locales', []))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
