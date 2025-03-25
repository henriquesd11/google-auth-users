<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class RedirectNotFoundMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->status() == 404) {
            return redirect('/api');
        }

        return $response;
    }
}
