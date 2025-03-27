<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class RedirectNotFoundMiddleware
 *
 * Middleware responsável por redirecionar requisições que resultam em 404 para a rota /api.
 *
 * @package App\Http\Middleware
 */
class RedirectNotFoundMiddleware
{
    /**
     * Manipula uma requisição.
     *
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->status() == 404) {
            return redirect('/api');
        }

        return $response;
    }
}
