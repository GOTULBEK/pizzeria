<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
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
        $response = $next($request);

        $response->header('Access-Control-Allow-Origin', 'http://localhost:8082')
                 ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
                 ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, X-Auth-Token, Cookie')
                 ->header('Access-Control-Allow-Credentials', 'true');

        if ($request->getMethod() === "OPTIONS") {
            $response->setStatusCode(204);
        }

        return $response;
    }
}
