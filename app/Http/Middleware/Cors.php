<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        // Configura os cabeçalhos CORS
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', env('FRONTEND_URL'));
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Max-Age', '86400');

        // Responde ao preflight (OPTIONS)
        if ($request->isMethod('OPTIONS')) {
            return response()->json('OK', 200)
                ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        return $response;
    }
}
